<?php
namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Admin;
use \App\Flash;
use \App\Auth;

class Login extends \Core\Controller {

    public function before(){
        
    }
    public function indexAction(){
        if(Auth::getAdmin()) {
            Flash::addMessage('Вы уже авторизированы!', Flash::INFO);
            
            $this->redirect('/admin');
        }
        View::renderTemplate("Admin/Login/index.html");
    }

    public function createAction(){
        if(Auth::getAdmin()) {
            Flash::addMessage('Вы уже авторизированы!', Flash::INFO);
            
            $this->redirect('/admin');
        }
        $user = Admin::authenticate($_POST['email'], $_POST['password']);

        if($user) {
            Auth::login($user);

            Flash::addMessage('Вход выполнен!');
            
            $this->redirect("/admin/home/index");

        }else{
            
            Flash::addMessage('Вход не выполнен, попробуйте еще раз!',Flash::WARNING);
            View::renderTemplate('Admin/Login/index.html',[
                'email' => $_POST['email']
            ]);
        }
    }
     /**
     * Log out a user
     * 
     * @return void
     */
    public function destroyAction()
    {
        Auth::logout();
        $this->redirect('/admin/login/show-logout-message');
        
    }
    /**
     * Show a "logged out" flash message and redirect to the homepage. Necessary to use the flash messages
     * as they use the session and at the end of the logout method (destroyAction) the session is destroyed
     * sa a new action needs to be called in order to use the session
     * 
     * @return void
     */
    public function showLogoutMessageAction() 
    {
        Flash::addMessage('Выход выполнен!');

        $this->redirect('/admin/login/index');
    }

}

?>