<?php
namespace App;

use \App\Config;
/** Generate random tokens
  *
  */

class Token{
	/**
  	* The token value
  	* @param string value (optional)  A token value
  	* @var array
  	*/
  	protected $token;

  	 /**
     * Class constructor. Create a new random token
     * @return void
     */
    public function __construct($token_value = null){

    	if($token_value){
    		$this->token = $token_value;
    	}else
    		$this->token = bin2hex(openssl_random_pseudo_bytes(16)); // 16 bytes = 128 bits = 32 hex characters  
    }

    /**
     * Get the token value
     * @return string - the token
     */
    public function getValue(){
     	return $this->token;
    }

    /**
     * Get the hash of the token
     * @return string - the  hashed value
     */
    public function getHash(){
     	return hash_hmac('sha256', $this->token, \App\Config::SECRET_KEY);	// sha 256 = 64 chars
    }
}