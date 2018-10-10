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
     * log in a user
     *
     * @return void
     */
    public function createAction()
    {
    	$user = User::authenticate($_POST['email'], $_POST['password']);
    	if($user){

    		Auth::login($user);
            Flash::addMessage('Login Successful');
    		$this->redirect(Auth::getReturnToPage());    		
    	
    	}else{
            Flash::addMessage('Login unsuccessful, please try again',Flash::WARNING);
    		View::renderTemplate('Login/new.html',[
    			'email' => $_POST['email']
    		]);
    	}
        //View::renderTemplate('Login/new.html');
    }

     /**
     * log out a user
     *
     * @return void
     */
    public function destroyAction(){
    	
		Auth::logout();
        $this->redirect('/login/show-logout-message');
    }

    /**
     * Show a logged out message. Necessary since logout Auth call will destroy all sessions.
     * Calling another action will create a new session and show the flash message.
     *
     * @return void
     */

    public function showLogoutMessageAction(){
        
        Flash::addMessage('Logout Successful');
        $this->redirect('/');
    }
}