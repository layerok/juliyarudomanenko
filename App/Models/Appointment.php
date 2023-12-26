<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * @property string $format;
 * @property string $purchase_date;
 * @property Customer $customer
 * @property Service $service
 */
class Appointment extends Model
{
    public $timestamps = false;
    public $fillable = [
        'format',
        'purchase_date'
    ];

    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo {
        return $this->belongsTo(Service::class);
    }
}