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
    public function newAction(){
        View::renderTemplate('Signup/new.html');
    }

	/**
     * Sign up a new user
     *
     * @return void
     */
     public function createAction()
    {
        if($_FILES['user_avatar']['size']!=0)
            $avatar =  $_FILES['user_avatar'];
        else
            $avatar = null;

        $user = new User($_POST);
        if($user->save($avatar)){
            if(($avatar && user::uploadAvatar($user->email,$avatar)) || !$avatar){
                Flash::addMessage('Registered Successfully, Please login!');
            }else{
                Flash::addMessage('Registered Successfully, Please login! Uploaded Avatar file not allowed. Please try uploading Avatar later through Profile page');
            }

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

    public function updateAction()
    {
        if ($_FILES['user_avatar']['size'] != 0)
            $avatar = $_FILES['user_avatar'];
        else
            $avatar = null;

        $user = new User($_POST);
        if($user->updateProfilePage($avatar) && (empty($user->errors))){
            Flash::addMessage('Updated Successfully');
            $this->redirect('/museum/gamify/users/profile');
        }
        View::renderTemplate('User/profile.html', array(
            'user' => $user));
    }
}