<?php
namespace App\Support;

use Verot\Upload\Upload;

class File {

    public static function delete($path): bool
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $path)) {
                return unlink($_SERVER['DOCUMENT_ROOT'] . $path);
            }
        }
        return false;
    }

}