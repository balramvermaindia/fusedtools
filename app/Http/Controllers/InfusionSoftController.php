<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Infusionsoft;
use App\UsersIsAccounts;
use App\UsersImportsData;
use App\UsersImports;
use Excel;
use App\UsersImportsDuplicateData;
use Auth; 

class InfusionSoftController extends Controller
{
	 private $authUser = array();
    
    function __construct() 
    {
		$this->authUser = Auth::user();
	}
    public function index() {
		
		$upload_status 	= UsersImports::where('upload_status', "pending")->first();
		
		if ( count($upload_status) ) {
			$users_imports_id   = $upload_status->id;
			$is_account_id 	  	= UsersImports::where('id', $users_imports_id)->value('is_account_id');
			$infusionsoft 		= new Infusionsoft\Infusionsoft(array(
				'clientId'    	=> config('infusionsoft.clientId'),
				'clientSecret' 	=> config('infusionsoft.clientSecret'),
				'redirectUri'  	=> config('infusionsoft.redirectUri'),
			));
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
			
			$db_rows_count 		= UsersImportsData::where('users_import_id', $users_imports_id)->max('row_number');
			$filters 			= UsersImports::where('id', $users_imports_id)->first();
			$duplicate_filter 	= $filters->filter_duplicate;
			$filter_company 	= $filters->filter_company;
			$filter_contact 	= $filters->filter_contact;
		
			for($rowid=1; $rowid<=$db_rows_count; $rowid++)
			{
				switch ($duplicate_filter) {
					
					case 1:
						$rEmail = UsersImportsData::where('users_import_id', $users_imports_id)->where('row_number', $rowid)->where('infusionsoft_field', 'Email')->value('value');
						
						if(!empty($rEmail)) {
							$query_fields = array('Email' => $rEmail);
							$returnFields = array('Id');
							$is_result = $infusionsoft->data()->query("Contact",10,0,$query_fields,$returnFields,'Id',true);
							if(is_array($is_result) && count($is_result) > 0){
								//duplicate found, add in duplicate table
								$contact_is_id = $is_result[0]['Id'];
								$create = array(
									'users_import_id' => $users_imports_id,
									'row_number' => $rowid,
									'infusionsoft_id' => $contact_is_id
								);
								$account = UsersImportsDuplicateData::create($create);
							}
						}
						break;
						
					case 2:
						$rFName = UsersImportsData::where('users_import_id', $users_imports_id)->where('row_number', $rowid)->where('infusionsoft_field', 'FirstName')->value('value');
						$rLName = UsersImportsData::where('users_import_id', $users_imports_id)->where('row_number', $rowid)->where('infusionsoft_field', 'LastName')->value('value');
						
						if(!empty($rFName) && !empty($rLName)) {
							$query_fields = array('FirstName' => $rFName, 'LastName' => $rLName);
							$returnFields = array('Id');
							$is_result = $infusionsoft->data()->query("Contact",10,0,$query_fields,$returnFields,'Id',true);
							if(is_array($is_result) && count($is_result) > 0){
								//duplicate found, add in duplicate table
								$contact_is_id = $is_result[0]['Id'];
								$create = array(
									'users_import_id' => $users_imports_id,
									'row_number' => $rowid,
									'infusionsoft_id' => $contact_is_id
								);
								$account = UsersImportsDuplicateData::create($create);
							}
						}
						break;
						
					case 3:
						$rEmail = UsersImportsData::where('users_import_id', $users_imports_id)->where('row_number', $rowid)->where('infusionsoft_field', 'Email')->value('value');
						$rPhone1 = UsersImportsData::where('users_import_id', $users_imports_id)->where('row_number', $rowid)->where('infusionsoft_field', 'Phone1')->value('value');
						$rPhone2 = UsersImportsData::where('users_import_id', $users_imports_id)->where('row_number', $rowid)->where('infusionsoft_field', 'Phone2')->value('value');
						
						if(!empty($rEmail) && (!empty($rPhone1) || !empty($rPhone2))) {
							$query_fields = array();
							$query_fields['Email']=$rEmail;
							if(!empty($rPhone1))
							$query_fields['Phone1']=$rPhone1;
							if(!empty($rPhone2))
							$query_fields['Phone2']=$rPhone2;
							$returnFields = array('Id');
							$is_result = $infusionsoft->data()->query("Contact",10,0,$query_fields,$returnFields,'Id',true);
							if(is_array($is_result) && count($is_result) > 0){
								//duplicate found, add in duplicate table
								$contact_is_id = $is_result[0]['Id'];
								$create = array(
									'users_import_id' => $users_imports_id,
									'row_number' => $rowid,
									'infusionsoft_id' => $contact_is_id
								);
								$account = UsersImportsDuplicateData::create($create);
							}
						}
						break;
						
					case 4:
						$rPhone1 = UsersImportsData::where('users_import_id', $users_imports_id)->where('row_number', $rowid)->where('infusionsoft_field', 'Phone1')->value('value');
						$rPhone2 = UsersImportsData::where('users_import_id', $users_imports_id)->where('row_number', $rowid)->where('infusionsoft_field', 'Phone2')->value('value');
						
						if(!empty($rPhone1) || !empty($rPhone2)) {
							$query_fields = array();
							if(!empty($rPhone1))
							$query_fields['Phone1']=$rPhone1;
							if(!empty($rPhone2))
							$query_fields['Phone2']=$rPhone2;
							$returnFields = array('Id');
							$is_result = $infusionsoft->data()->query("Contact",10,0,$query_fields,$returnFields,'Id',true);
							if(is_array($is_result) && count($is_result) > 0){
								//duplicate found, add in duplicate table
								$contact_is_id = $is_result[0]['Id'];
								$create = array(
									'users_import_id' => $users_imports_id,
									'row_number' => $rowid,
									'infusionsoft_id' => $contact_is_id
								);
								$account = UsersImportsDuplicateData::create($create);
							}
						}
						break;
						
					case 5:
						$rEmail = UsersImportsData::where('users_import_id', $users_imports_id)->where('row_number', $rowid)->where('infusionsoft_field', 'Email')->value('value');
						$rFName = UsersImportsData::where('users_import_id', $users_imports_id)->where('row_number', $rowid)->where('infusionsoft_field', 'FirstName')->value('value');
						
						if(!empty($rEmail) && !empty($rFName)) {
							$query_fields = array('FirstName' => $rFName, 'Email' => $rEmail);
							$returnFields = array('Id');
							$is_result = $infusionsoft->data()->query("Contact",10,0,$query_fields,$returnFields,'Id',true);
							if(is_array($is_result) && count($is_result) > 0){
								//duplicate found, add in duplicate table
								$contact_is_id = $is_result[0]['Id'];
								$create = array(
									'users_import_id' => $users_imports_id,
									'row_number' => $rowid,
									'infusionsoft_id' => $contact_is_id
								);
								$account = UsersImportsDuplicateData::create($create);
							}
						}
						break;
				}
			}
			//code ends to check duplicate data
			$tags 			= UsersImports::where('id', $users_imports_id)->value('selected_tags');
			$tags_array 	= unserialize($tags);
			$selected_tags = array();
			if ( $tags_array ) {
				foreach($tags_array as $ptag)
				{
					if(is_numeric($ptag))
					{
						$selected_tags[] = $ptag;
					}
					else
					{
						$table = "ContactGroup";
						$values = array(
							'GroupName' => $ptag,
						);
						$new_tag_id = $infusionsoft->data()->add($table, $values);
						$selected_tags[] = $new_tag_id;
					}
				}
			}
		
			$import_data = UsersImportsData::where('users_import_id', $users_imports_id)->orderBy('row_number', 'asc')->orderBy('field_order', 'asc')->get();
			$import_data_count = UsersImportsData::where('users_import_id', $users_imports_id)->max('row_number');
			if($import_data_count > 0)
			{
				$idx = 0;
				$this_row = $import_data[0]->row_number;
				$formatted_data = array();
				$csv_data = array();
				
				foreach($import_data as $row)
				{
					$record_row = $row->row_number;
					if($this_row == $record_row)
					{
						$formatted_data[$record_row][$row->infusionsoft_field]=$row->value;
						$csv_data[$record_row][$row->csv_field]=$row->value;
					}
					else
					{
						$this_row = $record_row;
						$idx++;
						$formatted_data[$record_row][$row->infusionsoft_field]=$row->value;
						$csv_data[$record_row][$row->csv_field]=$row->value;
					}
				}
				
				$rows_processed = array();
				
				foreach($formatted_data as $frowno => $fdata)
				{
					$is_Duplicate_Contact = UsersImportsDuplicateData::where('row_number', $frowno)->where('users_import_id', $users_imports_id)->count();
					if($filter_contact == "both" || $filter_contact=="update")
					{
						if( $is_Duplicate_Contact )
						{
							//need to update contact data
							$contact_is_id = UsersImportsDuplicateData::where('row_number', $frowno)->where('users_import_id', $users_imports_id)->value('infusionsoft_id');
							if($contact_is_id)
							{
								//if count foreach tags addtag
								if( count($selected_tags) > 0 )
								{
									foreach ($selected_tags as $tagID)
									{
										$contactId = $contact_is_id;
										$tagId     = $tagID;
										$result = $infusionsoft->contacts()->addToGroup($contactId, $tagId);
									}
								}
								if ($filter_company != "ignore") {
									if (array_key_exists('Company', $fdata))
									{
										if ( $filter_company == "both" || $filter_company == "match" ) {
											$company_name = $fdata['Company'];
											$query_fields = array('Company' => $company_name);
											$returnFields = array('Id');
											$is_company_id = $infusionsoft->data()->query("Company",10,0,$query_fields,$returnFields,'Id',true);
											if($is_company_id)
											{
												$fdata['CompanyID'] = $is_company_id;
											}
											else
											{
												if ($filter_company == "both") {
													$table = "Company";
													$values = array(
														'Company' => $company_name,
													);
													$is_company_id = $infusionsoft->data()->add($table, $values);
													$fdata['CompanyID'] = $is_company_id;
												}
											}
										} else {
											$company_name = $fdata['Company'];
											$query_fields = array('Company' => $company_name);
											$returnFields = array('Id');
											$is_company_id = $infusionsoft->data()->query("Company",10,0,$query_fields,$returnFields,'Id',true);
											if($is_company_id)
											{
												//$fdata['CompanyID'] = $is_company_id;
											}
											else
											{
												$table = "Company";
												$values = array(
													'Company' => $company_name,
												);
												$is_company_id = $infusionsoft->data()->add($table, $values);
												$fdata['CompanyID'] = $is_company_id;
											}
										}
									}
								}
								$infusionsoft->contacts()->update($contact_is_id, $fdata);
								array_push($rows_processed,$frowno);
							}
						}
						else
						{
							if( $filter_contact == "both" )
							{
								//good to add as new
								$contact_is_id = $infusionsoft->contacts()->add($fdata);
								array_push($rows_processed,$frowno);

								//if count foreach tags addtag
								if( count($selected_tags) > 0 )
								{
									foreach ($selected_tags as $tagID)
									{
										$contactId = $contact_is_id;
										$tagId     = $tagID;
										$result = $infusionsoft->contacts()->addToGroup($contactId, $tagId);
									}
								}
								if ($filter_company != "ignore") {
									if (array_key_exists('Company', $fdata))
									{
										if ( $filter_company == "both" || $filter_company == "match") {
											$company_name = $fdata['Company'];
											$query_fields = array('Company' => $company_name);
											$returnFields = array('Id');
											$is_company_id = $infusionsoft->data()->query("Company",10,0,$query_fields,$returnFields,'Id',true);
											if($is_company_id)
											{
												$fdata['CompanyID'] = $is_company_id;
											}
											else
											{
												if ( $filter_company == "both" ) {
													$table = "Company";
													$values = array(
														'Company' => $company_name,
													);
													$is_company_id = $infusionsoft->data()->add($table, $values);
													$fdata['CompanyID'] = $is_company_id;
												}
											}
										} else {
											$company_name = $fdata['Company'];
											$query_fields = array('Company' => $company_name);
											$returnFields = array('Id');
											$is_company_id = $infusionsoft->data()->query("Company",10,0,$query_fields,$returnFields,'Id',true);
											if($is_company_id)
											{
												//$fdata['CompanyID'] = $is_company_id;
											}
											else
											{
												$table = "Company";
												$values = array(
													'Company' => $company_name,
												);
												$is_company_id = $infusionsoft->data()->add($table, $values);
												$fdata['CompanyID'] = $is_company_id;
											}
										}
									}
								}
								
								if(isset($fdata['CompanyID']) && !empty($fdata['CompanyID']))
								{
									$cdata = array('CompanyID' => $fdata['CompanyID']);
									$infusionsoft->contacts()->update($contact_is_id, $cdata);
								}
							}
						}
					}
					else
					{
						if( $is_Duplicate_Contact = 0)
						{//good to add as new
								//good to add as new
								$contact_is_id = $infusionsoft->contacts()->add($fdata);
								array_push($rows_processed,$frowno);

								//if count foreach tags addtag
								if( count($selected_tags) > 0 )
								{
									foreach ($selected_tags as $tagID)
									{
										$contactId = $contact_is_id;
										$tagId     = $tagID;
										$result = $infusionsoft->contacts()->addToGroup($contactId, $tagId);
									}
								}
								if ( $filter_company != "ignore" ) {
									if (array_key_exists('Company', $fdata))
									{
										if ( $filter_company == "both" || $filter_company == "match") {
											$company_name = $fdata['Company'];
											$query_fields = array('Company' => $company_name);
											$returnFields = array('Id');
											$is_company_id = $infusionsoft->data()->query("Company",10,0,$query_fields,$returnFields,'Id',true);
											if($is_company_id)
											{
												$fdata['CompanyID'] = $is_company_id;
											}
											else
											{
												if ( $filter_company == "both" ) {
													$table = "Company";
													$values = array(
														'Company' => $company_name,
													);
													$is_company_id = $infusionsoft->data()->add($table, $values);
													$fdata['CompanyID'] = $is_company_id;
												}
											}
										} else {
											$company_name = $fdata['Company'];
											$query_fields = array('Company' => $company_name);
											$returnFields = array('Id');
											$is_company_id = $infusionsoft->data()->query("Company",10,0,$query_fields,$returnFields,'Id',true);
											if($is_company_id)
											{
												//$fdata['CompanyID'] = $is_company_id;
											}
											else
											{
												$table = "Company";
												$values = array(
													'Company' => $company_name,
												);
												$is_company_id = $infusionsoft->data()->add($table, $values);
												$fdata['CompanyID'] = $is_company_id;
											}
										}
									}
								}
								if(isset($fdata['CompanyID']) && !empty($fdata['CompanyID']))
								{
									$cdata = array('CompanyID' => $fdata['CompanyID']);
									$infusionsoft->contacts()->update($contact_is_id, $cdata);
								}
								
						}
					}
					
				}
				 
				$excel_processed = array();
				$excel_skipped   = array();
				foreach( $csv_data as $rowno => $fieldName) {
					if( in_array( $rowno, $rows_processed ) ) {
						array_push($excel_processed,$csv_data[$rowno]);
					} else {
						array_push($excel_skipped,$csv_data[$rowno]);
					}
				}
				
				$excel_data = array("processed"=>$excel_processed, "skipped"=>$excel_skipped);
				//create excel sheet with 2 sheets Processed Records and Skipped Records
				
				
				$result = Excel::create('ImportRecoreds', function($excel) use($excel_data) {
				
					$excel->sheet('Processed Records', function($sheet) use($excel_data) {
						$sheet->fromArray($excel_data['processed']);
					});
					
					$excel->sheet('Skipped Records', function($sheet) use($excel_data) {
							$sheet->fromArray($excel_data['skipped']);
					});

				})->store('xls');
				
				if ($result) {
					// send email to user with this excel sheet attachment as "Import Report.xls"
					$htmlbody        = "Please find the attachment.";
					$htmlbody 	    .= "\r\n\r\n\r\n\r\nThanks";
					$htmlbody       .= "\r\nFusedTools";
					$to 	  		= $this->authUser->email; //Recipient Email Address
					$subject  		= " Import data Report"; //Email Subject
					$rn 	  		= "\r\n";
					$headers  		= 'From: FusedTools Support <admin@fusedtools.com>';
					//$headers 		.= 'Mime-Version: 1.0' . $rn;
					$random_hash 	= md5(date('r', time()));
					$headers 		.= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\"";
					//$headers 		.= 'X-Mailer: PHP/' . phpversion();
					//$headers 		.= $rn;
					$attachment = chunk_split(base64_encode(file_get_contents(public_path('assets/uploads/exported_csv_files/ImportRecoreds.xls')))); // Set your file path here
					
					//define the body of the message.
					$message = "--PHP-mixed-$random_hash\r\n"."Content-Type: multipart/alternative; boundary=\"PHP-alt-$random_hash\"\r\n\r\n";
					$message .= "--PHP-alt-$random_hash\r\n"."Content-Type: text/plain; charset=\"iso-8859-1\"\r\n"."Content-Transfer-Encoding: 7bit\r\n\r\n";

					//Insert the html message.
					$message .= $htmlbody;
					$message .="\r\n\r\n--PHP-alt-$random_hash--\r\n\r\n";
					
					//include attachment
					$message .= "--PHP-mixed-$random_hash\r\n"."Content-Type: application/zip; name=\"ImportRecoreds.xls\"\r\n"."Content-Transfer-Encoding: base64\r\n"."Content-Disposition: attachment\r\n\r\n";
					$message .= $attachment;
					$message .= "/r/n--PHP-mixed-$random_hash--";

					//send the email
					$mail = mail( $to, $subject , $message, $headers );
				}

				UsersImports::where('id', $users_imports_id)->update(['upload_status' => 'success']);
				echo "success";
			} else {
				echo "no import data ";
			}
			
		}
	}
}
