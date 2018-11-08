<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

use App\Models\User;
use \Core\View;
/**
 * GameType controller
 *
 * PHP version 7.0
 */
class Manageaccount extends \Core\Controller
{
    public function newAction()
    {
        $users = User::getAllUsers();
        View::renderTemplate('Admin/accountsearch.html', array('users' => $users));
    }

    public function userAction()
    {
        $user = User::getUserByName($_GET['user']);
        View::renderTemplate('Admin/profile.html', array('user' => $user));
    }

    public function blockAction() {
        User::blockUser($_GET['user_id'], $_GET['block']);
        $this->redirect('/museum/gamify/manageaccount/new');
    }

    public function editAction() {

    }

    public function changeAction() {
        $_GET['user_id'];
    }

}