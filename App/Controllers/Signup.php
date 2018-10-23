<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Signup extends \Core\Controller
{
	/**
     * Show the signup page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

	/**
     * Sign up a new user
     *
     * @return void
     */
     public function createAction()
    {
    	//var_dump($_POST); 
        $user = new User($_POST);

        if($user->save()){
            $this->redirect('/museum/gamify/signup/success');
        }else{
            View::renderTemplate('Signup/new.html', array(
                'user' => $user
            ));
            //var_dump($user->errors);
        }        
    }

    /**
     * Show the signup success page
     *
     * @return void
     */
     public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }
}