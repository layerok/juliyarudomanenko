<?php

namespace App\Models;

use App\Support\File;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name;
 * @property string $id;
 * @property string $price;
 * @property string $duration;
 * @property string $description;
 * @property string $image;
 *
 */
class Service extends Model
{
    public $timestamps = false;

    public $fillable = [
        'name',
        'id',
        'price',
        'duration',
        'description',
        'image'
    ];

    public function delete() {
        File::delete($this->image);
        return parent::delete();
    }
}