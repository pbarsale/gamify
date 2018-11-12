<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

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
            Flash::addMessage('Registered Successfully, Please login!');
            if($_SESSION['admin'])
                $this->redirect('/museum/gamify/admin');
            else
                $this->redirect('/museum/gamify');


        }else{

            if($_SESSION['admin'])
                View::renderTemplate('Admin/index.html', array(
                'user' => $user));
            else
                View::renderTemplate('Home/index.html', array(
                    'user' => $user));
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