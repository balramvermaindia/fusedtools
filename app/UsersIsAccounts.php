<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersIsAccounts extends Model
{
    protected $table = 'users_is_accounts';
    protected $fillable = ['user_id','access_token','referesh_token','expire_date','account','active'];
    
    
    
}
