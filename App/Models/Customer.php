<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @property string $phone;
 * @property string $name
 * @property integer $id
 */
class Customer extends Model
{
    public $timestamps = false;
    public $fillable = ['phone', 'name'];

    public function save(array $options = []): bool
    {
        $this->phone = static::convertPhoneToInternational($this->phone);
        return parent::save();
    }

    public static function convertPhoneToInternational($phone): string {
        $onlyNumbers = preg_replace('/[^\d]/', '', $phone);
        
        preg_match('/\d{9}$/', $onlyNumbers,$matches);

        return "+380".$matches[0];
    }

    public static function findByPhone($phone): ?Customer
    {
        return self::where('phone', '=', static::convertPhoneToInternational($phone))->first();
    }
}