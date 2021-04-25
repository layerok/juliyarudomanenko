<?php

namespace App\Models;

use PDO;
use \App\Models\Customer;
use \App\Telegram;
use \App\Config;

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

    public static function getAll(){
        $sql = "SELECT appointments.id as rowId,
                       appointments.*,
                       appointments.id AS appointmentsId,
                       services.name AS serviceName, 
                       customers.name AS customerName,
                       customers.phone
                FROM appointments
                    LEFT JOIN customers
                        ON appointments.customer_id = customers.id
                    LEFT JOIN services
                        ON appointments.service_id = services.id
                ORDER BY appointments.purchase_date DESC";

        $db = static::getDB();
        $stmt = $db->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->fetchAll();
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
        // recaptcha
        if(Config::ENABLE_PRODUCTION){
            if(empty($this->{'g-recaptcha-response'})){
                // Флажок рекапчи не был отмечен
               
                $this->errors['recaptcha_failed'] = "Подтвердите, что вы не робот!";
            }else{
                
                $verify = curl_init();
                curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify?secret=".Config::SECRET_KEY."&response=".$this->{'g-recaptcha-response'});
                curl_setopt($verify, CURLOPT_POST, true);
                curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($verify);
                $response = json_decode($response,true);
                if($response['success'] == true){
                    
                }else{
                    // ответ рекапчи не вернул success = true
                    $this->errors['recaptcha_failed'] = "Ответ рекапчи не вернул успех";
                }
            }
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

            $post_data = [
                "name" => [
                    "value" => $this->name ?? "",
                    "description" => "Имя",
                    "emoji"=>"\xE2\x9C\x8F"
                ],
                "phone" => [
                    "value" => $this->phone ?? "",
                    "description" => "Телефон",
                    "emoji"=>"\xF0\x9F\x93\x9E"
                ],
                "format" => [
                    "value" => $this->format ?? "",
                    "description" => "Формат встречи",
                    "emoji"=>"\xF0\x9F\x93\xA8"
                ]
            ];

            $telegram = new Telegram(Config::BOT_TOKEN,Config::CHAT_ID);
            $telegram->send("Запись на прием",$post_data);

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
    /**
     * delete model by id 
     *
     * @return array
     */
    public function delete($id)
    {
        
        $sql = "DELETE FROM appointments
                WHERE id = :id";
        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id",$id,PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        return $stmt->execute();
        
    }

    

    
    
    
}