<?php

namespace App\Models;

use PDO;
use \Verot\Upload\Upload;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Certificate extends \Core\Model
{
    public $path = "/img/certificates/";
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
     * Get all the certificates as an associative array
     *
     * @return array
     */
    public static function getAll()
    {
        $sql ="SELECT * FROM certificates";

        $db = static::getDB();
        $stmt = $db->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->fetchAll();
    }
    /**
     * Get model by id as an associative array
     *
     * @return array
     */
    public function getOne($id)
    {
        $sql = "SELECT * FROM certificates
                WHERE id = :id";
        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id",$id,PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }
    /**
     * Save the model with the current property values
     * 
     * @return
     */
    public function save() 
    {
        $this->validate();

        if(empty($this->errors)) {

            $sql = "INSERT INTO certificates (name,  image)
                    VALUES (:name, :image)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
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

            
            $sql = "UPDATE certificates 
                    SET name = :name, 
                        image = :image
                    WHERE id = :id";
                    

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':image', $this->image_name, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        }
        return false;
    }

    public function validate($id = null){
        if(empty($this->name)){
            $this->errors[] = "Введите 'Название' Сертификата";
        }
        $handle = new Upload($this->files['image'], 'ru_RU');
        $handle->allowed = array('image/*');

        if ($handle->uploaded) {

            if(empty($this->errors)){
                $handle->image_resize   = true;
                $handle->image_ratio_y  = true;
                $handle->image_x        = 720;
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
        
        $record = $this->getOne($id);
        $this->deleteFile($this->path.$record->image);

        $sql = "DELETE FROM certificates 
                WHERE id = :id";
        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id",$id,PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        return $stmt->execute();
        
    }
    
    
}