<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property string $message
 */
class CustomerMessage extends Model
{
    public $timestamps = false;
    public $fillable = ['message'];

    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class);
    }

}