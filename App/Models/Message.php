<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $content;
 * @property string $name;
 */
class Message extends Model
{
    public $timestamps = false;

    public $fillable = [
        'content',
        'name'
    ];
}