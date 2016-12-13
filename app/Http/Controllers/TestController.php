<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\UsersIsAccounts;
use Infusionsoft;

use Excel;

class TestController extends Controller
{
	
	public function ss() {
		
		$result = Excel::create('Import_csv', function($excel) {

			$excel->sheet('Processed Records', function($sheet) {
				$sheet->fromArray(array(
					array('data1' => 'data2'),
					array('data1' => 'data4')
				));
			});
			 // Our second sheet
			$excel->sheet('Skipped Records', function($sheet) {
					$sheet->fromArray(array(
					array('data1', 'data2'),
					array('data3', 'data4')
				));
			});

		})->store('xls');
		
		echo "<pre>"; print_r($result); die;

	}
	
	public function getAllTags(){
		$infusionsoft = new Infusionsoft\Infusionsoft(array(
				'clientId'     => config('infusionsoft.clientId'),
				'clientSecret' => config('infusionsoft.clientSecret'),
				'redirectUri'  => config('infusionsoft.redirectUri'),
			));
			
			//$is_account_id = $request->session()->get('CSV_import.is_account_id');
			$client_is_account = UsersIsAccounts::where('id','1')->first();
			$access_token = $client_is_account->access_token;
			$infusionsoft->setToken(unserialize($access_token));

			if ($infusionsoft->getToken()) {
				try {
					//nothing
				} catch (\Infusionsoft\TokenExpiredException $e) {
					$infusionsoft->refreshAccessToken();
				}
			}
			
			
			$table = "Product";
				$values = array(
					'ProductName' => "testing's",
				);
				$product_id = $infusionsoft->data()->add($table, $values);
				dd($product_id);

			$is_fields = config('infusionsoft.infusionsoftFields');
			
			$query_fields = array('FormId' =>'-1');
			$returnFields = array('Name');
			$result = $infusionsoft->data()->query("DataFormField",1000,0,$query_fields,$returnFields,'Id',true);
			if(count($result))
			{
				foreach($result as $res)
				{
					$fieldname = $res['Name'];
					$is_fields[$fieldname] = 'String';
				}
			}
			ksort($is_fields, SORT_STRING);
			
			
			dd($is_fields);
			
			$contactId = 262;
			$tagId     = 108;
			$result = $infusionsoft->contacts()->addToGroup($contactId, $tagId);
			//~ echo "<pre>"; print_r($result); die;
			
			//~ 
			$table = "ContactGroup";
			$values = array(
				'GroupName' => "Testtag",
			);
			//~ //$result = $infusionsoft->data()->add($table, $values);
			//echo $result; die;
			//~ $query_fields = array('GroupName' =>'%');
			//~ $returnFields = array('Id','GroupName');
			//~ $result = $infusionsoft->data()->query("ContactGroup",1000,0,$query_fields,$returnFields,'Id',true);
			//~ echo "<pre>"; print_r($result); die;
			
			//check if company exists
			
			$query_fields = array('Company' =>'testing');
			$returnFields = array('Id','Company');
			$result = $infusionsoft->data()->query("Company",1000,0,$query_fields,$returnFields,'Id',true);
			if( $result ) {
				$contactId = 262;
				$data      = array(
					'Company' 	=> 'testing',
					'CompanyID'	=> '280'
				);
				$result = $infusionsoft->contacts()->update($contactId, $data);
				echo "update". $result; die;
			} else {
				$table = "Company";
				$values = array(
					'Company' => 'testing',
				);
				$compant_id = $infusionsoft->data()->add($table, $values);
				$data      = array(
					'Company' 	=> 'testing',
					'CompanyID'	=> $compant_id
				);
				$contactId = 262;
				$result = $infusionsoft->contacts()->update($contactId, $data);
				echo "create & update". $result; die;
			}
			//echo "<pre>"; print_r($result); die;
	}
	
}
