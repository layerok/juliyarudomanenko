<?php
namespace App\Support;

class Str {

    public static  function snake(string $input): string {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    public static function plural(string $input): string {
        return $input . 's';
    }

}