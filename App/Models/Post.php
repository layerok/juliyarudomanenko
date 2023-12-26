<?php

namespace App\Models;

use App\Support\File;
use \Illuminate\Database\Eloquent\Model;


/**
 * @property int $id;
 * @property string $title;
 * @property string $slug;
 * @property string $content;
 * @property string $image;
 * @property string $meta_title;
 * @property string $meta_description;
 */
class Post extends Model
{
    public $timestamps = false;

    public $fillable = [
        'id',
        'title',
        'slug',
        'content',
        'image',
        'meta_title',
        'meta_description'
    ];

    public static function slugExists(string $slug, ?int $ignore_id = null): bool
    {
        return !!Post::where([
            ['slug', '=', $slug],
            ['id', '!=', $ignore_id]
        ])->first();
    }

    public function delete() {
        File::delete($this->image);
        return parent::delete();
    }

}