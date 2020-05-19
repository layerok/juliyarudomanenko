<?php

namespace App\Models;

use PDO;
use \App\Models\Customer;


/**
 * appointment model
 *
 * PHP version 7.0
 */
class Appointment extends \Core\Model
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
     * Validate current property values, adding valiation error messages to the errors array property
     * 
     * @return void
     */
    public function validate()
    {
        // Name
        if($this->name == '') {
            $this->errors[] = 'Введите имя';
        }

        // phone format
        if(!preg_match('/^\+?3?8?\(?0\d{2}\)?\-?\d{3}\-?\d{2}\d{2}$/',$this->phone)) {
            $this->errors[] = 'Введите телефон в формате +380xxxxxxx';
        }
    }

        /**
     * Save the appointment model with the current property values
     * 
     * @return
     */
    public function save() 
    {
        $this->validate();

        if(empty($this->errors)) {

            $customer = new Customer($_POST);  
            $customer_id =  $customer->phoneExists($customer->phone);
            if(!$customer_id){
                $customer_id = $customer->save();
            }
            $today = date("Y-m-d H:i:s");
            

            $sql = 'INSERT INTO appointments (customer_id, service_id, purchase_date, format)
                    VALUES (:customer_id, :service_id, :purchase_date, :format)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
            $stmt->bindValue(':service_id', $this->service_id, PDO::PARAM_INT);
            $stmt->bindValue(':purchase_date', $today, PDO::PARAM_STR);
            $stmt->bindValue(':format', $this->format, PDO::PARAM_STR);
            

            return $stmt->execute();
        }
        return false;
    }

    

    
    
    
}