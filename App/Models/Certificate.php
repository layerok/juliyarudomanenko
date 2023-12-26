<?php

namespace App\Models;

use App\Support\File;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $image
 */
class Certificate extends Model {
    public $timestamps = false;

    public $fillable = [
        'name',
        'image'
    ];

    public function delete() {
        File::delete($this->image);
        return parent::delete();
    }
}