<?php

namespace App\Models;

use PDO;

/**
 * admin model
 *
 * PHP version 7.0
 */
class Admin extends \Core\Model
{

    /**
     * Error messages
     * 
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     * 
     * @param array $date Initial property values
     * 
     * @return void
     */
    public function __construct($data = []) 
    {
        foreach($data as $key => $value) {
            $this->$key = $value;
        };
    }
    
    /**
     * See if a user record already exists with the specified email
     * 
     * @param string $email email address to search for
     * 
     * @return boolean True if a record already exists with the specidied email, false otherwise
     * @param string $ignore_id Return false anyway if the record found has this ID
     */
    public static function emailExists($email, $ignore_id = null) 
    {
        $user = static::findByEmail($email);

        if($user) {
            if($user->id != $ignore_id) {
                return true;
            }
        }
        return;
    }

    /**
     * Find a user model by email address
     * 
     * @param string $email email address to search for
     * 
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM admins WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
    /**
     * Authenticate a user by email and password
     * 
     * @param string $email email address
     * @param string $password password
     * 
     * @return mixed. The user object or false if authentication fails
     */

    public static function authenticate($email, $password) 
    {
        $user = static::findByEmail($email);

        if($user) {
            if(password_verify($password, $user->password_hash)) {
                return $user;
            }
        }
        return false;
    }
    /**
     * Find a user model by ID
     * 
     * @param string $id The user ID
     * 
     * @return mixed Admin object if found, false otherwise
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM admins WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
     
    
}
