<?php

namespace App\Models;

use PDO;
use \Verot\Upload\Upload;



/**
 * Example user model
 *
 * PHP version 7.0
 */
class Service extends \Core\Model
{
    public $path = "/img/services/";
    public $currency = " грн.";
    public $errors = [];

     /**
     * Class constructor
     * 
     * @param array $date Initial property values
     * 
     * @return void
     */
    public function __construct($data = [],$files = []) 
    {
        foreach($data as $key => $value) {
            $this->$key = $value;
        };
        $this->files = $files;
    }
    /**
     * Get all the services as an associative array
     *
     * @return array
     */
    public static function getAll()
    {
        $sql ="SELECT * FROM services";

        $db = static::getDB();
        $stmt = $db->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->fetchAll();
    }
    /**
     * Get service by id as an associative array
     *
     * @return array
     */
    public function getOne($id)
    {
        $sql = "SELECT * FROM services
                WHERE id = :id";
        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id",$id,PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }
    /**
     * Save the service model with the current property values
     * 
     * @return
     */
    public function save() 
    {
        $this->validate();

        if(empty($this->errors)) {

            $this->description = htmlspecialchars($this->description);
            $sql = "INSERT INTO services (name, price, duration, description, image)
                    VALUES (:name, :price, :duration,'".$this->description."', :image)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':price', $this->price, PDO::PARAM_INT);
            $stmt->bindValue(':duration', $this->duration, PDO::PARAM_STR);
            
            $stmt->bindValue(':image', $this->image_name, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }
    /**
     * Save the service model with the current property values
     * 
     * @return
     */
    public function saveChanges($id) 
    {
        $this->validate($id);

        if(empty($this->errors)) {

            $this->description = htmlspecialchars($this->description);
            $sql = "UPDATE services 
                    SET name = :name, 
                        price = :price, 
                        duration = :duration, 
                        description = '".$this->description."' , 
                        image = :image
                    WHERE id = :id";
                    

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':price', $this->price, PDO::PARAM_INT);
            $stmt->bindValue(':duration', $this->duration, PDO::PARAM_STR);
            
            $stmt->bindValue(':image', $this->image_name, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        }
        return false;
    }

    public function validate($id = null){
        if(empty($this->name)){
            $this->errors[] = "Введите 'Название' услуги";
        }
        if(empty($this->price)){
            $this->errors[] = "Введите 'Стоимость' услуги";
        }
        if(empty($this->duration)){
            $this->errors[]= "Введите 'Продолжительность' услуги";
        }
        if(empty($this->description)){
            $this->errors[] = "Введите 'Описание' услуги";
        }

        $handle = new Upload($this->files['image'], 'ru_RU');
        $handle->allowed = array('image/*');

        if ($handle->uploaded) {

            if(empty($this->errors)){
                $handle->image_resize   = true;
                $handle->image_ratio_y  = true;
                $handle->image_x        = 360;
                $handle->process($_SERVER['DOCUMENT_ROOT'].$this->path);
                if($handle->processed){
                    if(!empty($id)){
                        $record = $this->getOne($id);
                        $this->deleteFile($this->path.$record->image);
                    }
                    $this->image_name = $handle->file_dst_name;
                }else{
                    $this->errors[] = $handle->error;
                }
            }

        }else{
            if(!empty($id)){
                $record = $this->getOne($id);
                $this->image_name = $record->image;
            }else{
                $this->errors[] = $handle->error;
            }
        }

    }
    /**
     * delete service by id as an associative array
     *
     * @return array
     */
    public function delete($id)
    {
        
        $record= $this->getOne($id);
        $this->deleteFile($this->path.$record->image);
        $sql = "DELETE FROM services 
                WHERE id = :id";
        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id",$id,PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        return $stmt->execute();
        
    }
    
}