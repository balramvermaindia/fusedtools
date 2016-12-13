<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersImports extends Model
{
    protected $table 	= "users_imports";
    protected $fillable = ['user_id','start_date','csv_file','is_account_id','filter_display','filter_contact','filter_company','filter_duplicate'];
}
