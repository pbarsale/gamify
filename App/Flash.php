<?php
namespace App;

use \App\Models\User;
/** Flash Messages for one time display using sessions
  *
  */

class Flash{

	/**
     * Success message type
     * @var string
     */
	const SUCCESS = 'success';
	const INFO = 'info';
	const WARNING = 'warning';



	/**
     * Add a message
     * @param string $message The message content
     * @return void
     */

	public static function addMessage($message, $type = 'success'){
		if(!isset($_SESSION['flash_notifications'])){
			$_SESSION['flash_notifications'] = array();

			// Append message to the array
			$_SESSION['flash_notifications'][] = array(
				'body' => $message,
				'type' => $type
			);
		}	
	}

	/**
     * Get flash messages if any.
     * 
     * @return mixed An array of messages or null.
     */

	public static function getMessages(){
		if(isset($_SESSION['flash_notifications'])){
			$messages = $_SESSION['flash_notifications'];
			unset($_SESSION['flash_notifications']);
			return $messages;
		}	
	}
}