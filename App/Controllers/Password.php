<?php

namespace App\Controllers;
use \Core\View;
use \App\Models\User;

/**
 * Reset Password controller
 *
 * PHP version 5.3.3
 */
class Password extends \Core\Controller
{
	/**
     * Show the forgotten password page
     *
     * @return void
     */
    public function forgotAction(){
        View::renderTemplate('Password/forgot.html');
    }

    /**
     * Process the reset request
     *
     * @return void
     */
    public function requestResetAction(){
        if(isset($_POST['email'])){
            User::sendPasswordReset($_POST['email']);
        }
        View::renderTemplate('Password/reset_requested.html');
    }

    /**
     * Process for the reset link
     *
     * @return void
     */
    public function resetAction(){
        $token = $this->route_params['token'];
        $user = $this->getUserOrExit($token);
        View::renderTemplate('Password/reset.html', array('token' => $token));
    }

    /**
     * Update the DB and reset the password
     *
     * @return void
     */
    public function resetPasswordAction(){

        if(!isset($_POST['token'])){
            View::renderTemplate('Password/reset_success.html');
        }
        $token = $_POST['token'];
        $user = $this->getUserOrExit($token);

        if($user->resetPassword($_POST['password'],$_POST['confirmPwd'])){
            View::renderTemplate('Password/reset_success.html');
        }else{
            View::renderTemplate('Password/reset.html', array('token' => $token,'user' => $user));
        }
    }

    /**
     * Find the user model associated with the token or end the request with a message
     * @param string $token 
     * @return mixed - user object if found and valid token, otherwise null
     */
    public function getUserOrExit($token){
        $user = User::findByPasswordReset($token);

        if($user){
            return $user;
        }else{
            View::renderTemplate('Password/token_expired.html');            
            exit;
        }
    }
}