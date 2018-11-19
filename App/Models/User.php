<?php

namespace App\Models;

use PDO;
use \Datetime;
use \App\Token;
use \App\Mail;
use \Core\View;
/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{

    const FILEPATH = "/museum/gamify/";
    /**
     * Error messages
     * @var array
     */
    public $errors = array();


    /**
     * @param array $data Initial Property Value
     * @return void
     */
    public function __construct($data =array()){
        foreach($data as $key => $value){
            $this->$key = $value;
        };
    }

    /**
     * Get all the users as an associative array
     *
     * @return void
     */
    public function save($avatar){

        $this->validate($avatar);
        if(empty($this->errors)){

            $isadmin = $_SESSION['admin'];
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $sql = "Insert into users(name,member_id,email,password,birth_date,isadmin)
                            values(:name,:member_id,:email,:password,:birth_date,:isadmin)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name',$this->name,PDO::PARAM_STR);
            $stmt->bindValue(':member_id',$this->member_id,PDO::PARAM_STR);
            $stmt->bindValue(':email',$this->email,PDO::PARAM_STR);
            $stmt->bindValue(':password',$password_hash,PDO::PARAM_STR);
            $stmt->bindValue(':birth_date',$this->birth_date,PDO::PARAM_STR);
            $stmt->bindValue(':isadmin',$isadmin,PDO::PARAM_BOOL);
            return $stmt->execute();
        }
        return false; 
    }


    /**
    * Validate current property values, adding validation error messages to the errors array property
     *
     * @return void
     */
    
    public function validate($avatar){
        if (empty($this->name) || empty($this->email) || empty($this->password) || empty($this->confirmPwd) || empty($this->birth_date)) {
            $this->errors[] = 'Empty fields not allowed';
        }
        
        if (!preg_match("/^[a-zA-Z ]*$/", $this->name)){
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

        if($avatar){
            $avatar_type = $avatar['type'];
            if(!($this->validateImage($avatar_type))) {
                $this->errors[] = 'Avatar should be of file type - JPEG, PNG, GIF';
            }
        }

        $dob = new DateTime($this->birth_date);
        $now = new DateTime();

        if($now<$dob){
            $this->errors[] = 'Birth date cannot be future date';
        }else if($now->diff($dob)->y < 1){
            $this->errors[] = 'Should be 1 year old or above';
        }else if($now->diff($dob)->y >199){
            $this->errors[] = 'Age seems to be quite old, please check again';
        }
    }


    /**
    * Validate password values, required for reset password
     *
     * @return void
     */
    
    public function validatePassword(){
        if (empty($this->password) || empty($this->confirmPwd)) {
            $this->errors[] = 'Empty fields not allowed';
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
    }

    /**
    * See if a user record already exists with same email.
     *
     * @param $email - email to search
     * @return boolean - true if record exists, or else return false
     */


    public static function emailExists($email){
        return static::findByEmail($email) !== false;
    }


    /**
    * Find a user model by email address
     *
     * @param $email - email to search for
     * @return user object if found, otherwise false
     */


    public static function findByEmail($email){

        $sql = "SELECT * from users where email=:email";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email',$email,PDO::PARAM_STR); 

        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }


    /**
     * Find a user model by id
     *
     * @param $id string The user id
     * @return mixed user object if found, otherwise false
     */


    public static function findById($id){

        $sql = "SELECT * from users where id=:id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id',$id,PDO::PARAM_INT); 

        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Authenticate the user by email and password
     *
     * @param string $email - email to search
     * @param string $password - password to verify
     * @return mixed : The user object or false if authentication fails
     */


    public static function authenticate($email,$password){

        $user = static::findByEmail($email);
        if($user){
            if(password_verify($password,$user->password)){
                if($user->isadmin==$_SESSION['admin'])
                    return $user;
            }
        }
        return false;
    }

    /**
     * Remember the login by inserting a new unique token into the
     * remembered_logins table
     * @return boolean : True if login was remembered successfully, else false
     */


    public function rememberLogin(){

        $token = new Token();
        $hashed_token = $token->getHash();

        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60*60*24*30; // 30 days from now.

        $sql = "Insert into remembered_logins(token_hash,user_id,expires_at)
                            values(:token_hash,:user_id,:expires_at)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash',$hashed_token,PDO::PARAM_STR);
        $stmt->bindValue(':user_id',$this->id,PDO::PARAM_INT);
        $stmt->bindValue(':expires_at',date('Y-m-d H:i:s',$this->expiry_timestamp),PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Send reset password instructions to the user specified
     * @param string $email The email address
     * @return void
     */


    public static function sendPasswordReset($email){
        $user = static::findByEmail($email);

        if($user){
            if($user->startPasswordReset()){
                $user->sendPasswordResetEmail();
            }
        }
    }

    /**
     * Start the password reset process by generating a new token and expiry
     * @return void
     */
    protected function startPasswordReset(){
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->reset_token = $token->getValue();

        $expiry_timestamp = time() + 60*60*2; // 2 hours from now.

        $sql = 'UPDATE users 
                SET password_reset_hash = :token_hash,
                    password_reset_expiry = :expires_at
                WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash',$hashed_token,PDO::PARAM_STR);
        $stmt->bindValue(':expires_at',date('Y-m-d H:i:s',$expiry_timestamp),PDO::PARAM_STR);
        $stmt->bindValue(':id',$this->id,PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Send password reset email to the user
     * @return void
     */
    protected function sendPasswordResetEmail(){
        
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/museum/gamify/password/reset/'.$this->reset_token;

        $text = View::getTemplate('Password/reset_email.txt',array('url' => $url));
        $html = View::getTemplate('Password/reset_email.html',array('url' => $url));
        
        Mail::send($this->email,'Password reset request',$text,$html);
    }

    /**
     * Find user model by password reset token and expiry
     * @param string $token password reset sent to user
     * @return mixed. User model if found and has not expired, otherwise null
     */
    public static function findByPasswordReset($token){

        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = "SELECT * from users where password_reset_hash=:token_hash";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':token_hash',$hashed_token,PDO::PARAM_STR); 

        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());

        $stmt->execute();
        $user = $stmt->fetch();

        if($user){
            // check password reset token has not expired
            if(strtotime($user->password_reset_expiry)>time()){
                return $user;
            }
        }
    }


    /** 
     * Reset Password
     * @param string password - The new password
     * @return boolean
     */
    public function resetPassword($password,$confirmPwd){
        $this->password = $password;
        $this->confirmPwd = $confirmPwd;
        $this->validatePassword();

        if(empty($this->errors)){
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'UPDATE users
                    SET password=:password_hash,
                        password_reset_hash = NULL,
                        password_reset_expiry = NULL
                    where id=:id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id',$this->id,PDO::PARAM_INT);
            $stmt->bindValue(':password_hash',$password_hash,PDO::PARAM_STR);
            return $stmt->execute();
        }
        return false;
    }

    public static function getAllUsers() {
        $sql = "SELECT * FROM users";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getUserByName($name) {
        $sql = "SELECT * from users where name=:name";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Find the age of the user model
     *
     * @param Birth date of the user
     */


    public function getAgeFromBirthDate(){

        $dobObject = new DateTime($this->birth_date);
        $nowObject = new DateTime();
        $diff = $dobObject->diff($nowObject);
        $this->age = $diff->y;
        return;
    }

    public static function getAllUsersByAgeGroup($currUserAgeGroup) {
        $sql = "SELECT * FROM users where isblocked=:isblocked and isadmin=:isadmin";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isblocked', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':isadmin', false, PDO::PARAM_BOOL);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $users = $stmt->fetchAll();

        $usersByAge = array();

        foreach ($users as $user) {
            $user->getAgeFromBirthDate();
            $userAgeGroup = AgeGroup::getAgeGroupIdByAge($user->age);
            if($userAgeGroup and self::isAgeWithinRange($userAgeGroup, $currUserAgeGroup)) {
                array_push($usersByAge, $user);
            }
        }
        return $usersByAge;
    }

    public static function getAllUsersByUserAge($id) {
        $user = self::findById($id);
        $user->getAgeFromBirthDate();
        $userAgeGroup = AgeGroup::getAgeGroupIdByAge($user->age);
        $users = self::getAllUsersByAgeGroup($userAgeGroup);
        return $users;
    }

    public static function isAgeWithinRange($userAge, $ageGroup) {
        return ($userAge->min === $ageGroup->min and $userAge->max === $ageGroup->max);
    }

    public static function blockUser($user_id, $block)
    {
        $sql = "UPDATE users SET isblocked=:isblocked where id=:id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':isblocked', !$block, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public  function validateImage($avatar_type) {
        return preg_match('/^image\\/p?jpeg$/i', $avatar_type) or
            preg_match('/^image\\/gif$/i', $avatar_type) or
            preg_match('/^image\\/(x-)?png$/i', $avatar_type);
    }

    public static function uploadAvatar($email, $avatar){

        $user = self::findByEmail($email);
        $avatar_tmp = $avatar['tmp_name'];
        $filepath = "images/" . $user->id."_avatar.jpg";

        if(move_uploaded_file($avatar_tmp, $filepath)){
            $sql = "UPDATE users SET avatar=:avatar where id=:id";
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $user->id, PDO::PARAM_INT);
            $stmt->bindValue(':avatar',self::FILEPATH.$filepath, PDO::PARAM_STR);
            return $stmt->execute();
        }else{
            return false;
        }
    }

    public static function setUserAvatar(){
        if(isset($_SESSION['user_id'])){
            return self::getuserAvatarById($_SESSION['user_id']);
        }
        return null;
    }

    public static function getuserAvatarById($id){

        $sql = "SELECT * from users where id=:id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id',$id,PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());
        $stmt->execute();

        $user = $stmt->fetch();
        return $user->avatar;
    }


    public function validateProfilePage($avatar){

        if (empty($this->name)){
            $this->errors[] = 'Empty Name not allowed';
        }

        if (!preg_match("/^[a-zA-Z ]*$/", $this->name)){
            $this->errors[] = 'First and Last name should contain only alphabets';
        }

        if($avatar){
            $avatar_type = $avatar['type'];
            if(!($this->validateImage($avatar_type))) {
                $this->errors[] = 'Avatar should be of file type - JPEG, PNG, GIF';
            }
        }
    }

    public function updateProfilePage($avatar)
    {
        $this->validateProfilePage($avatar);
        if (empty($this->errors)) {
            if($avatar){
                if(!user::uploadAvatar($this->email,$avatar)){
                    $this->errors[] = 'Problem uploading the avatar, please try different avatar';
                }
            }

            $sql = "UPDATE users SET name=:name, member_id=:member_id where id=:id";
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':member_id', $this->member_id, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        }
        return false;
    }
}
