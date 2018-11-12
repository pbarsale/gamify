<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

use \Core\View;

/**
 * GameType controller
 *
 * PHP version 7.0
 */
class Admin extends \Core\Controller
{
    public function newAction(){
        $_SESSION['admin'] = true;
        View::renderTemplate('Admin/index.html');
    }
}