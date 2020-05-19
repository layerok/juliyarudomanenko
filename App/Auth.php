<?php

namespace App;

use \Core\View;
use \App\Models\Admin;


/**
 * Authentication
 *
 * PHP version 7.0
 */
class Auth
{

    /**
     * Login the admin
     * 
     * @param Admin $admin The admin model
     * @param boolean $remember_me Remember the login if tru
     *
     * @return void
     */
    public static function login($admin)
    {
        session_regenerate_id(true);

        $_SESSION['admin_id'] = $admin->id;

        
    }

    /**
     * Logout the admin
     * 
     * @return void
     */
    public static function logout() 
    {
        // Удаляем все переменные сессии.
        $_SESSION = [];

        // Если требуется уничтожить сессию, также необходимо удалить сессионные cookie.
        // Замечание: Это уничтожит сессию, а не только данные сессии!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }

        // Наконец, уничтожаем сессию.
        session_destroy();

        static::forgetLogin();
    }
    /**
     * Remember the originally-requested page in the session
     * 
     * @return void
     */
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }
    /**
     * Get originally-requested page to return to after requiring login, or default to the homepage
     * 
     * @return void
     */ 
    public static function getReturnToPage()
    {
        return $_SESSION['return_to'] ?? '/';
    }
    /**
     * Get the current logged-in admin, from the session or the remember-me cookie
     * 
     * @return mixed The admin model or null if not logged in
     */
    public static function getAdmin() 
    {
        if(isset($_SESSION['admin_id'])) {
            return Admin::findByID($_SESSION['admin_id']);
        }else{
            return static::loginFromRememberCookie();
        }
    }

    /**
     * Login the admin from a remembered login cookie
     * 
     * @return mixed The admin model if login cookie found; null otherwise
     */
    protected static function loginFromRememberCookie()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;

        if($cookie) {
            $remembered_login = RememberedLogin::findByToken($cookie);
            
            if($remembered_login  && ! $remembered_login->hasExpired()) {
                $admin = $remembered_login->getAdmin();

                static::login($admin, false);

                return $admin;
            }
        }
    }
    /**
     * Forget the remembered login, if present
     * 
     * @return void
     */
    protected static function forgetLogin()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;

        if($cookie) {
            $remembered_login = RememberedLogin::findByToken($cookie);

            if($remembered_login) {
                $remembered_login->delete();
            }
            setcookie('remember_me', '', time()-3600);//set to expire in the past
        }
    }
}
