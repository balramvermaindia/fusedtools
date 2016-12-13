<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use App\UsersIsAccounts;
use Excel;
use App\UsersImports;
use Infusionsoft;
use App\UsersImportsData;
use App\UsersImportsDuplicateData;
use Illuminate\Support\Facades\DB;

class ImportCSVController extends Controller
{
    private $authUser = array();
    
    function __construct() 
    {
		$this->authUser = Auth::user();
	}
	
	public function importStep1(Request $request) 
	{
		$parm = $request->parm;
		if( empty($parm) ) {
			//$request->session()->put('CSV_import', array());
			$request->session()->forget('CSV_import');

		}
		$userID				  = $this->authUser->id;
		$infusionsoftAccounts = UsersIsAccounts::where('user_id',$userID)->pluck('account','id');
		return view('importCSV.step1',compact('infusionsoftAccounts'));
	}
	
	public function importStep2(Request $request)
	{
		if ($request->isMethod('post') && empty($request->parm) ){
			$rules	= [ 
			'account' 		=> 'required',
			'csv_file' 		=> 'required'
			];
			$this->validate($request,$rules);
			
			$is_account_id 		= $request->account;
			$file				= $request->file('csv_file');
			$file_ext			= strtolower($file->getClientOriginalExtension());
			
			//do validate extension dont forget
			//upload and move file
			$destination_path = public_path()."/assets/uploads/csv_files";
			$upload_image	  = time().'.'.$file_ext;
			$file->move($destination_path, $upload_image);
			
			//set fields in session
			$CSV_import = array(
				'is_account_id' => $is_account_id,
				'csv_file'      => $upload_image
			);
			$request->session()->put('CSV_import', $CSV_import);
		}
		$CSV_import = $request->session()->get('CSV_import');
		//read csv/excel file to get field names
		$file_fields_arr = '';
		$result = Excel::load("assets/uploads/csv_files/".$CSV_import['csv_file'])->first()->toArray();
		if ( $result ) {
			$file_fields_arr = array_keys( $result );
		}
		

		//connect with IS to get custom fields of selected IS account
		$infusionsoft = new Infusionsoft\Infusionsoft(array(
			'clientId'     => config('infusionsoft.clientId'),
			'clientSecret' => config('infusionsoft.clientSecret'),
			'redirectUri'  => config('infusionsoft.redirectUri'),
		));
		
		$is_account_id     = $request->session()->get('CSV_import.is_account_id');
		$client_is_account = UsersIsAccounts::where('id',$is_account_id)->first();
		$access_token      = $client_is_account->access_token;
		$infusionsoft->setToken(unserialize($access_token));

		if ($infusionsoft->getToken()) {
			try {
				//nothing
			} catch (\Infusionsoft\TokenExpiredException $e) {
				$infusionsoft->refreshAccessToken();
			}
		}


		$IS_fields 	      = config('infusionsoft.infusionsoftFields');
		$query_fields     = array('FormId' =>'-1');
		$returnFields     = array('Name');
		$result           = $infusionsoft->data()->query("DataFormField",1000,0,$query_fields,$returnFields,'Id',true);
		if(count($result))
		{
			foreach($result as $res)
			{
				$fieldname = $res['Name'];
				$IS_fields[$fieldname] = 'String';
			}
		}
		ksort($IS_fields, SORT_STRING);

		
		if ( $request->session()->get('CSV_import') ) {
			return view('importCSV.step2',compact('file_fields_arr', 'IS_fields'));
		} else {
			return redirect('import-step1');
		}
	}


	function addWithDupCheck($infusionsoft) {
		$contact = array('FirstName' => 'John', 'LastName' => 'Doe', 'Email' => 'johndoe@mailinator.com');
		return $infusionsoft->contacts->addWithDupCheck($contact, 'Email');
	}	
	public function importStep3(Request $request) 
	{
		$csv_fields 		 = $request->csv_fields;
		$infusionsoft_fields = $request->infusionsoft_fields;
		
		if ( !$request->session()->get('CSV_import') ) {
			return redirect('/import-step1')->with('error','Error- Session has expired.');
		} 
		$userID				= $this->authUser->id;
		$CSV_import			= $request->session()->get('CSV_import');
		$fields_arr			= array();
		if ( count($infusionsoft_fields) ) {
			
			foreach ( $infusionsoft_fields as $key => $field ) {
				if ( !empty($field) ) {
					$fields_arr[$csv_fields[$key]] = $field;
				}
			}
			
			$create				= array(
				'user_id' 		=> $userID,
				'start_date'	=> date('Y-m-d h:i:s'),
				'csv_file'		=> $CSV_import['csv_file'],
				'is_account_id' => $CSV_import['is_account_id']
			);
			$user_import 		= UsersImports::create($create);
		
			if ( !$user_import ) {
				return back()->with('error','Error occurs while completing the request. Please try after sometime');
			}
		
		
			//read csv/excel file to upload  data to UsersImportsdata table
			$csv_file_data = Excel::load("assets/uploads/csv_files/".$CSV_import['csv_file'])->get()->toArray();
			
			if ($csv_file_data) {
				$row_number = 1;
				foreach( $csv_file_data as $row ) {
					$field_order = 1;
					foreach( $row as $csv_field => $value ){
						if ( array_key_exists($csv_field,$fields_arr) ) {
							if($value==null)
								continue;
								
							$infusionsoft_field 	= $fields_arr[$csv_field];
							$infusionsoft_field_id 		= 0;
							$infusionsoft_field_type 	= 'default';
							$create = array(
								'users_import_id' 			=> $user_import->id,
								'csv_field'		 	 		=> $csv_field,
								'infusionsoft_field'  		=> $infusionsoft_field,
								'infusionsoft_field_id' 	=> $infusionsoft_field_id,
								'infusionsoft_field_type'  	=> $infusionsoft_field_type	,
								'value' 					=> $value,
								'row_number' 				=> $row_number,
								'field_order' 				=> $field_order
							);
							UsersImportsData::create($create);
							$field_order = $field_order + 1;
						}
					}
					$row_number = $row_number+1;
				}
			}
			$CSV_import['users_import_id']  = $user_import->id;
			$CSV_import['fields_arr'] 		= $fields_arr;
			$request->session()->put('CSV_import', $CSV_import);
		}
		
		
		// get all tags from infusionsoft account
		$infusionsoft = new Infusionsoft\Infusionsoft(array(
			'clientId'     => config('infusionsoft.clientId'),
			'clientSecret' => config('infusionsoft.clientSecret'),
			'redirectUri'  => config('infusionsoft.redirectUri'),
		));
		$is_account_id = $request->session()->get('CSV_import.is_account_id');
		$client_is_account = UsersIsAccounts::where('id',$is_account_id)->first();
		$access_token = $client_is_account->access_token;
		$infusionsoft->setToken(unserialize($access_token));

		if ($infusionsoft->getToken()) {
			try {
				//nothing
			} catch (\Infusionsoft\TokenExpiredException $e) {
				$infusionsoft->refreshAccessToken();
			}
		}
		$query_fields = array('GroupName' =>'%');
		$returnFields = array('Id','GroupName');
		$tags_arr = $infusionsoft->data()->query("ContactGroup",1000,0,$query_fields,$returnFields,'Id',true);
		
		$final_arr = array();
		if( count ($tags_arr) ) {
			foreach( $tags_arr as $arr ) {
				$data['id'] = $arr['Id'];
				$data['GroupName'] = isset( $arr['GroupName'] ) ? $arr['GroupName']: '';
				array_push($final_arr, $data);
			}
		}
		$tags     = json_encode($final_arr);
		
		
		
		return view('importCSV.step3',compact('tags'));
	}
	
	//~ public function importStep4(Request $request)
	//~ {
		//~ if ( !$request->session()->get('CSV_import') ) {
			//~ return redirect('/import-step1')->with('error','Error- Session has expired.');
		//~ } 
		//~ echo "<pre>"; print_r($request->all()); die;
		//~ $userID								= $this->authUser->id;
		//~ $CSV_import							= $request->session()->get('CSV_import');
		//~ $users_imports_id					= $CSV_import['users_import_id'];
		//~ $update	= array(
				//~ "filter_display" 			=> $request->filter_display,
				//~ "filter_contact" 			=> $request->filter_contact,
				//~ "filter_company" 			=> $request->filter_company,
				//~ "filter_duplicate" 			=> $request->filter_duplicate
		//~ );
		//~ $user_import 						= UsersImports::where('user_id',$userID)->where('id',$users_imports_id)->update($update);
		//~ 
		//~ 
		//~ $CSV_import['filter_display'] 		= $request->filter_display;
		//~ $CSV_import['filter_contact']  		= $request->filter_contact;
		//~ $CSV_import['filter_company']  		= $request->filter_company;
		//~ $CSV_import['filter_duplicate']  	= $request->filter_duplicate;
		//~ 
		//~ $request->session()->put('CSV_import' ,$CSV_import );
		//~ //echo "<pre>"; print_r( $request->session()->get('CSV_import') );
//~ 
		//~ // get all tags from infusionsoft account
		//~ $infusionsoft = new Infusionsoft\Infusionsoft(array(
			//~ 'clientId'     => config('infusionsoft.clientId'),
			//~ 'clientSecret' => config('infusionsoft.clientSecret'),
			//~ 'redirectUri'  => config('infusionsoft.redirectUri'),
		//~ ));
		//~ $is_account_id = $request->session()->get('CSV_import.is_account_id');
		//~ $client_is_account = UsersIsAccounts::where('id',$is_account_id)->first();
		//~ $access_token = $client_is_account->access_token;
		//~ $infusionsoft->setToken(unserialize($access_token));
//~ 
		//~ if ($infusionsoft->getToken()) {
			//~ try {
				//~ //nothing
			//~ } catch (\Infusionsoft\TokenExpiredException $e) {
				//~ $infusionsoft->refreshAccessToken();
			//~ }
		//~ }
		//~ $query_fields = array('GroupName' =>'%');
		//~ $returnFields = array('Id','GroupName');
		//~ $tags_arr = $infusionsoft->data()->query("ContactGroup",1000,0,$query_fields,$returnFields,'Id',true);
		//~ 
		//~ $final_arr = array();
		//~ if( count ($tags_arr) ) {
			//~ foreach( $tags_arr as $arr ) {
				//~ $data['id'] = $arr['Id'];
				//~ $data['GroupName'] = isset( $arr['GroupName'] ) ? $arr['GroupName']: '';
				//~ array_push($final_arr, $data);
			//~ }
		//~ }
		//~ $tags     = json_encode($final_arr);
		//~ 
		//~ return view('importCSV.step4', compact('tags'));
		//~ 
	//~ }
	
	public function importStep4(Request $request)
	 {
		if ( !$request->session()->get('CSV_import') ) {
			return redirect('/import-step1')->with('error','Error- Session has expired.');
		}
		
		$userID								= $this->authUser->id;
		$CSV_import							= $request->session()->get('CSV_import');
		$users_imports_id					= $CSV_import['users_import_id'];
		$update	= array(
				"filter_display" 			=> $request->filter_display,
				"filter_contact" 			=> $request->filter_contact,
				"filter_company" 			=> $request->filter_company,
				"filter_duplicate" 			=> $request->filter_duplicate,
				"notification_email"        => $request->notification_email
		);
		$user_import 						= UsersImports::where('user_id',$userID)->where('id',$users_imports_id)->update($update);
		
		
		$CSV_import['filter_display'] 		= $request->filter_display;
		$CSV_import['filter_contact']  		= $request->filter_contact;
		$CSV_import['filter_company']  		= $request->filter_company;
		$CSV_import['filter_duplicate']  	= $request->filter_duplicate;
		
		$request->session()->put('CSV_import' ,$CSV_import );
		
		
		//connect with IS to get custom fields of selected IS account
		$infusionsoft = new Infusionsoft\Infusionsoft(array(
			'clientId'     => config('infusionsoft.clientId'),
			'clientSecret' => config('infusionsoft.clientSecret'),
			'redirectUri'  => config('infusionsoft.redirectUri'),
		));
		
		$is_account_id = $request->session()->get('CSV_import.is_account_id');
		$users_imports_id = $request->session()->get('CSV_import.users_import_id');
		$filter_contact = $request->session()->get('CSV_import.filter_contact');
		$filter_company = $request->session()->get('CSV_import.filter_company');
		$client_is_account = UsersIsAccounts::where('id',$is_account_id)->first();
		$access_token = $client_is_account->access_token;
		$infusionsoft->setToken(unserialize($access_token));


		if ($infusionsoft->getToken()) {
			try {
				//nothing
			} catch (\Infusionsoft\TokenExpiredException $e) {
				$infusionsoft->refreshAccessToken();
			}
		}
		
		$selected_tags = array();
		if ( $request->apply_tags == "yes" ) {
			$posted_tags  = $request->tags;
			$tags_array   = serialize( explode(",",$posted_tags) );
			$update       = array(
				'selected_tags' => $tags_array,
				);
			$userID				  = $this->authUser->id;
			$user_import = UsersImports::where('user_id',$userID)->where('id',$users_imports_id)->update($update);
			
		} 
		$account = $client_is_account->account;
		$view_data=array('message' => 'Your request is under progress. We will mail you after completion.','account' => $account);
		return view('importCSV.step5', $view_data);
	} 
}
