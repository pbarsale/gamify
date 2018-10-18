<?php
namespace App;

use \App\Models\User;
use \App\Models\RememberedLogin;
/** Authentication
  *
  */


class Auth{
	/**
     * @param User $user The User Model
     *
     * @return vpopmail_del_domain(domain)
     */

	public static function login($user,$remember_me){
		session_regenerate_id(true);
    	$_SESSION['user_id'] = $user->id;
    	if($remember_me){
    		if($user->rememberLogin()){
    			setcookie('remember_me', $user->remember_token, $user->expiry_timestamp, '/');
    		}  
    	}
	}

	/**
     * Logout a user
     * @return void
     */

	public static function logout(){

		// Unset all of the session variables.
		$_SESSION = array();

		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
		    $params = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 42000,
		        $params["path"], $params["domain"],
		        $params["secure"], $params["httponly"]
		    );
		}

		// Finally, destroy the session.
		session_destroy();
		static::forgetLogin();
	}

	/**
     * Remember the originally requested page in the session
     * @return void
     */

	public static function rememberRequestedPage(){

		$_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
	}


	/**
     * Get the originally requested page or default home page in the session
     * @return void
     */

	public static function getReturnToPage(){

	    if($_SESSION['admin'] )
	        $_SESSION['return_to'] = '/Gamify/login';
	    else
            $_SESSION['return_to'] = '/Admin/userhome.html';

		return $_SESSION['return_to'];
		//return $_SESSION['return_to'] ?? '/'; 
	}

	/**
     * Get the current logged-in user, from the session or the remember-me cookie
     * @return mixed The user model or null if not logged in
     */

	public static function getUser(){
		if(isset($_SESSION['user_id'])){
			return User::findById($_SESSION['user_id']);
		}else{
			return static::loginFromRememberCookie();
		}
	}

	/**
     * Login the user from a remember-me cookie
     * @return mixed The user model if cookie found else null
     */

	protected static function loginFromRememberCookie(){
		$cookie = false;
		//$cookie = $_COOKIE['remember_me'] ?? false;

		if($cookie){
			$remembered_login = RememberedLogin::findByToken($cookie);

			if($remembered_login && ! $remembered_login->hasExpired()){
				$user = $remembered_login->getUser();
				static::login($user,false);
				return $user;
			}
		}
	}

	/**
     * Forget the remembered login, if present
     * @return void
     */

	protected static function forgetLogin(){
		
		$cookie = false;
		//$cookie = $_COOKIE['remember_me'] ?? false;

		if($cookie){
			$remembered_login = RememberedLogin::findByToken($cookie);

			if($remembered_login){
				$remembered_login->delete();
			}
			setcookie('remember_me','',time() - 3600);	// set to expire in the past
		}
	}


}
