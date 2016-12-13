<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Infusionsoft;
use App\Http\Requests;
use App\UsersIsAccounts;

class RefreshTokensController extends Controller
{
    public function refreshISTokens() 
    {
		$token_refresh_date 	= date("Y-m-d h:i:s", strtotime('+4 hours'));
		$accounts     			= UsersIsAccounts::all();
		//echo $token_refresh_date; die;
		if ($accounts) {
			
			foreach ($accounts as $account) {
				
				$expire_date  = $account->expire_date;
				if( $expire_date <= $token_refresh_date ) {
					
					$infusionsoft = new Infusionsoft\Infusionsoft(array(
						'clientId'     => config('infusionsoft.clientId'),
						'clientSecret' => config('infusionsoft.clientSecret'),
						'redirectUri'  => config('infusionsoft.redirectUri'),
					));
					
					// retrieve the existing token object from storage
					$yourStoredToken = $account->access_token;
					$infusionsoft->setToken(unserialize($yourStoredToken));
					$response 		=  $infusionsoft->refreshAccessToken();
					
					//update the token to user's account
					if ( $response ) {
						$userID				= $account->user_id;
						$referesh_token 	= $response->refreshToken;
						$expire_after 		= $response->endOfLife;
						$expire_date	  	= date('Y-m-d h:i:s', $expire_after);
						$update = array(
							'access_token'	=>serialize($response),
							'referesh_token'=>$referesh_token,
							'expire_date' 	=> $expire_date,
							'updated_at'    => date('Y-m-d h:i:s')
						);
						
						UsersIsAccounts::where('user_id',$userID)->update($update);
						echo "Updated:$userID\n";
					} 
				} 
			}
			echo "done!";
		} 
	}
			
}

