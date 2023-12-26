<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookComment extends Model
{
    public $timestamps = false;
    public $fillable = ['link'];
}