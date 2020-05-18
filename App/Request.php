<?php 

namespace App;

class Request 
{
    public static function isAjax(){
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
                    && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
                    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    public static function isPost(){
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }
    
}