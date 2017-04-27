<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'users';

    protected $fillable = ['name', 'job'];
}
