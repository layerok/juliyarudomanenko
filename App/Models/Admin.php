<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id;
 * @property string $email;
 * @property string $name;
 */
class Admin extends Model
{
    public $timestamps = false;
    public $fillable = [
        'email',
        'name'
    ];

    public static function findByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    public static function findByID($id)
    {
        return self::where('id', $id)->first();
    }

    public static function emailExists($email, $ignore_id = null) 
    {
        $user = static::findByEmail($email);

        if($user) {
            if($user->id != $ignore_id) {
                return true;
            }
        }
    }

    public static function authenticate($email, $password) 
    {
        $user = static::findByEmail($email);

        if($user) {
            if(password_verify($password, $user->password_hash)) {
                return $user;
            }
        }
        return false;
    }

}
