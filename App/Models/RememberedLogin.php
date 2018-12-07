<?php

namespace App\Models;
use \App\Token;
/**
 * Remembered login model
 *
 * PHP version 7.0
 */
class RememberedLogin extends \Core\Model{

	/**
     * Find a remembered login model by the token
     *
     * @param string $token The remembered login token
     * @return mixed remembered user object if found, otherwise false
     */


    public static function findByToken($token){

    	$token = new Token($token);
    	$token_hash = $token->getHash();


        $sql = "SELECT * from remembered_logins where token_hash=:token_hash";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':token_hash',$token_hash,PDO::PARAM_STR); 

        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
 
        $stmt->execute();
        return $stmt->fetch(); 
    }

    /**
     * Get the user model associated with the id
     *
     * @return user model
     */


    public function getUser($token){

    	return User::findById($this->user_id);
    }

    /**
     * Set if the remember token has expired or not, based on the current system time
     *
     * @return boolean True if the token has expired, false otherwise
     */

    public function hasExpired(){
    	return strtotime($this->expires_at) < time();
    }

    /**
     * Delete this model
     *
     * @return void
     */

    public function delete(){
    	$sql = "DELETE from remembered_logins where token_hash=:token_hash";
    	$db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash',$this->token_hash,PDO::PARAM_STR); 
        $stmt->execute();
    }
}