<?php

namespace App\Controllers;
use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;


/**
 * Login controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller
{
	/**
     * Show the login page
     *
     * @return void
     */
    public function newAction(){
        //View::renderTemplate('Login/new.html');
        View::renderTemplate('Home/index.html');
    }

    /**
     * log in a user
     *
     * @return void
     */
    public function createAction()
    {
            $remember_me = isset($_POST['remember_me']);

            if(!isset($_POST['email']) || !isset($_POST['password'])){
                View::renderTemplate('Home/index.html',array(
                    'email' => '',
                    'remember_me' => $remember_me
                ));
            }

            $user = User::authenticate($_POST['email'], $_POST['password']);

            if($user){
                Auth::login($user,$remember_me);
                //Flash::addMessage('Login Successful');
                $this->redirect(Auth::getReturnToPage());

            }else{
                Flash::addMessage('Login unsuccessful, please try again',Flash::WARNING);
                // add admin code here
                View::renderTemplate('Home/index.html',array(
                    'email' => $_POST['email'],
                    'remember_me' => $remember_me
                ));
            }
    }

     /**
     * log out a user
     *
     * @return void
     */
    public function destroyAction(){
        Auth::logout();
        $this->redirect('/museum/gamify/login/show-logout-message');
    }

    /**
     * log out a user
     *
     * @return void
     */
    public function destroyAdminAction(){
        Auth::logout();
        Flash::addMessage('Logout Successful');
        $this->redirect('/museum/gamify/admin');
    }

    /**
     * Show a logged out message. Necessary since logout Auth call will destroy all sessions.
     * Calling another action will create a new session and show the flash message.
     *
     * @return void
     */

    public function showLogoutMessageAction(){
        Flash::addMessage('Logout Successful');
        $this->redirect('/museum/gamify/');
    }
}