<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersImportsData extends Model
{
    protected $table = "users_imports_data";
    protected $fillable = ['users_import_id','csv_field','infusionsoft_field','value','row_number','field_order'];
}
