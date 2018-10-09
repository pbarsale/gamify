<?php

namespace App\Models;

use PDO;
use \Datetime;


/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{

    /**
     * Error messages
     * @var array
     */
    public $errors = [];
 

    /**
     * @param array $data Initial Property Value
     * @return void
     */
    public function __construct($data){
        foreach($data as $key => $value){
            $this->$key = $value;
        };
    }

    /**
     * Get all the users as an associative array
     *
     * @return void
     */
    public function save(){

        $this->validate();

        if(empty($this->errors)){ 

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $sql = "Insert into users(name,member_id,email,password,birth_date)
                            values(:name,:member_id,:email,:password,:birth_date)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name',$this->name,PDO::PARAM_STR);
            $stmt->bindValue(':member_id',$this->member_id,PDO::PARAM_STR);
            $stmt->bindValue(':email',$this->email,PDO::PARAM_STR);
            $stmt->bindValue(':password',$password_hash,PDO::PARAM_STR);
            $stmt->bindValue(':birth_date',$this->birth_date,PDO::PARAM_STR);
            return $stmt->execute();
        }
        return false; 
    }


    /**
    * Validate current property values, adding validation error messages to the errors array property
     *
     * @return void
     */
    
    public function validate(){
        if (empty($this->name) || empty($this->email) || empty($this->password) || empty($this->confirmPwd) || empty($this->birth_date)) {
            $this->errors[] = 'Empty fields not allowed';
        }
        
        if (!preg_match("/^[a-zA-Z]*$/", $this->name)){
            $this->errors[] = 'First and Last name should contain only alphabets';
        }
        
        if(!filter_var($this->email,FILTER_VALIDATE_EMAIL)){
            $this->errors[] = 'Invalid email';
        }

        if($this->emailExists($this->email)){
            $this->errors[] = 'E-mail already exists';
        }

        if($this->password != $this->confirmPwd){
            $this->errors[] = 'Password must match confirmation';
        }

        if(strlen($this->password) < 6){
            $this->errors[] = 'Password enter at least 6 characters for the password';
        }

        if(!preg_match("/.*[a-z]+.*/i", $this->password)){
            $this->errors[] = 'Password needs at least one letter';
        }

        if(!preg_match("/.*\d+.*/i", $this->password)){
            $this->errors[] = 'Password needs at least one number';
        }

        $dob = new DateTime($this->birth_date);
        $now = new DateTime();
        if($now->diff($dob)->y < 2){
            $this->errors[] = 'Should be 2 year old or above';
        }
    }

    /**
    * See if a user record already exists with same email.
     *
     * @param $email - email to search
     * @return boolean - true if record exists, or else return false
     */


    protected function emailExists($email){

        $sql = "SELECT * from users where email=:email";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email',$email,PDO::PARAM_STR); 

        $stmt->execute();
        return $stmt->fetch()!==false;
    }



    /**
     * Get all the users as an associative array
     *
     * @return array
     */
    public static function getAll()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT id, name FROM users');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
