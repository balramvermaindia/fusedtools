<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersImportsDuplicateData extends Model
{
    protected $table = "users_imports_duplicate_data";
    protected $fillable = ['users_import_id','row_number', 'infusionsoft_id'];
}
