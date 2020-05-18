<?php

namespace App\Models;

use PDO;

/**
 * customer model
 *
 * PHP version 7.0
 */
class Customer extends \Core\Model
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
     * Save the user model with the current property values
     * 
     * @return
     */
    public function save() 
    {
        

        if(empty($this->errors)) {

            $this->phone = static::convertPhoneToInternational($this->phone);

            $sql = 'INSERT INTO customers (name, phone)
                    VALUES (:name, :phone)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':phone', $this->phone, PDO::PARAM_STR);
            

            $stmt->execute();

            return $db->lastInsertId();
            
        }
        return false;
    }


    public static function convertPhoneToInternational($phone){
        $onlyNumbers = preg_replace('/[^\d]/', '', $phone);
        
        $withoutInternationalCode = preg_match('/\d{9}$/', $onlyNumbers,$matches); 
        
        $convertedPhone = "+380".$matches[0];
        return $convertedPhone;
    }
/**
     * See if a customer record already exists with the specified phone
     * 
     * @param string $email email address to search for
     * 
     * @return boolean True if a record already exists with the specidied phone, false otherwise
     * @param string $ignore_id Return false anyway if the record found has this ID
     */
    public static function phoneExists($phone, $ignore_id = null) 
    {
        $customer = static::findByPhone($phone);
        if($customer) {
            if($customer->id != $ignore_id) {
                return $customer->id;
            }
        }
        return false;
    }

    /**
     * Find a user model by phone number
     * 
     * @param string $phone phone number to search for
     * 
     * @return mixed Customer object if found, false otherwise
     */
    public static function findByPhone($phone)
    {
        
        $phone = static::convertPhoneToInternational($phone);
        $sql = 'SELECT * FROM customers WHERE phone = :phone';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
}