<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use App\UsersIsAccounts;
use Infusionsoft;

class ManageAccountsController extends Controller
{
	 private $authUser 	 = '';
	 private $infusionsoft 	 = '';
	 function __construct()
	 {
		$this->authUser = Auth::user();
	 }


    public function listUsersAccount()
    {
		$userID 		= $this->authUser->id;
		$accounts 		= UsersIsAccounts::where('user_id',$userID)->get();
		return view('userAccounts/list_accounts',compact('accounts'));
	}
	
	public function addUsersAccount()
	{
		$this->infusionsoft = new Infusionsoft\Infusionsoft(array(
			'clientId'     => config('infusionsoft.clientId'),
			'clientSecret' => config('infusionsoft.clientSecret'),
			'redirectUri'  => config('infusionsoft.redirectUri'),
		));
		return redirect($this->infusionsoft->getAuthorizationUrl());
	}
	
	public function saveUsersAccount(Request $request) 
	{
		$response = array();
		$this->infusionsoft = new Infusionsoft\Infusionsoft(array(
			'clientId'     => config('infusionsoft.clientId'),
			'clientSecret' => config('infusionsoft.clientSecret'),
			'redirectUri'  => config('infusionsoft.redirectUri'),
		));
		//echo "<pre>"; print_r($request->all()); die;
		$code 	  = $request->code;
		
		if ( !$code ) {
			return redirect('/manage-accounts')->with('error','Error occurs while completing the request. Please try after sometime');
		}
		$response = $this->infusionsoft->requestAccessToken($code);
		//echo "<pre>"; print_r($response); die;
		if ( !$response ) {
			return redirect('/manage-accounts')->with('error','Error occurs while completing the request. Please try after sometime');
		}

		$userID 			= $this->authUser->id;
		$startTime 			= date("Y-m-d h:i:s");
		$access_token 		= $response->accessToken;
		$referesh_token 	= $response->refreshToken;
		$expire_after 		= $response->endOfLife;
		$expire_date	  	= date('Y-m-d h:i:s', $expire_after);
		$token_type         = $response->extraInfo['token_type'];
		$scope         		= $response->extraInfo['scope'];
		$scope_arr			= explode("|", $scope);	
		$account			= $scope_arr[1];	
		if ( $this->checkIfAccountExits($userID, $account) == true ) {
			
				$update = array(
					'access_token'	=>serialize($response),
					'referesh_token'=>$referesh_token,
					'expire_date' 	=> $expire_date
				);
				$account = UsersIsAccounts::where('user_id',$userID)->update($update);
			
		} else { 
				$create = array(
					'user_id' 		=> $userID,
					'access_token'	=>serialize($response),
					'referesh_token'=>$referesh_token,
					'expire_date' 	=> $expire_date,
					'account' 		=> $account
				);
				$account = UsersIsAccounts::create($create);
		}
		if ($account) {
			 return redirect('/manage-accounts')->with('success','Account added successfully');
		 } else {
			 return redirect('/manage-accounts')->with('error','Error occurs while completing the request. Please try after sometime');
		}
	}
	
	public function changeStatusOfAccount(Request $request)
	{
		$response  			= '';
		$userID    			= $this->authUser->id;
		$accountID 			= $request->accountID;
		$status    			= $request->status == "active" ? 0: 1;
		$update    			= array(
			'active' 		=> $status,
		);
		$result    			= UsersIsAccounts::where('user_id',$userID)->where('id',$accountID)->update($update);
		if ( $result ) {
			$accounts 		= UsersIsAccounts::where('user_id',$userID)->get();
			$response 		= view('userAccounts/list_accounts_ajax',compact('accounts'))->render();
		}
		return $response;
	}
	
	public function deleteAccount(Request $request)
	{
		$response  			= '';
		$userID    			= $this->authUser->id;
		$accountID 			= $request->accountID;
		$result   			= UsersIsAccounts::where('user_id',$userID)->where('id',$accountID)->delete();
		if ( $result ) {
			$accounts 		= UsersIsAccounts::where('user_id',$userID)->get();
			$request->session()->flash('success', 'Account deleted successfully');
			$response 		= view('userAccounts/list_accounts_ajax',compact('accounts'))->render();
		}
		$request->session()->flash('error', 'Error occurs while completing the request. Please try after sometime');
		return $response;
	}
	
	public function checkIfAccountExits( $user_id , $account )
	{
		$result				= '';
		$result   			= UsersIsAccounts::where('user_id',$user_id)->where('account',$account)->count();
		if( $result > 0) {
			return true;
		} else {
			return false;
		}
	}
}
