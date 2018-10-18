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
    public function newAction()
    {
        View::renderTemplate('Login/new.html');
    }

    /**
     * Show the Home page for admin
     *
     * @return void
     */
    public function newAdminAction()
    {
        $_SESSION['admin'] = true;
        $this->redirect('/Gamify/');
    }

    /**
     * log in a user
     *
     * @return void
     */
    public function createAction()
    {
    	$user = User::authenticate($_POST['email'], $_POST['password']);
        $remember_me = isset($_POST['remember_me']);

    	if($user){

    		Auth::login($user,$remember_me);
            // Remember the login code

            Flash::addMessage('Login Successful');

            if($_SESSION['admin'])
                View::renderTemplate('Admin/adminhome.html');
            else
                View::renderTemplate('User/userhome.html');

    		//$this->redirect(Auth::getReturnToPage());
    	
    	}else{
            Flash::addMessage('Login unsuccessful, please try again',Flash::WARNING);
    		View::renderTemplate('Login/new.html',array(
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
        $this->redirect('/Gamify/login/show-logout-message');
    }

    /**
     * Show a logged out message. Necessary since logout Auth call will destroy all sessions.
     * Calling another action will create a new session and show the flash message.
     *
     * @return void
     */

    public function showLogoutMessageAction(){
        
        Flash::addMessage('Logout Successful');
        $this->redirect('/Gamify/');
    }
}