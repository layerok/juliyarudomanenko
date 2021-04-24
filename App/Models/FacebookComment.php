<?php

namespace App\Models;

use PDO;


/**
 * Example facebook-comment model
 *
 * PHP version 7.0
 */
class FacebookComment extends \Core\Model
{
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
        }
    }
    /**
     * Get all the facebook comment as an associative array
     *
     * @return array
     */
    public static function getAll()
    {
        $sql =" SELECT *,
                       id as rowId 
                FROM facebook_comments";

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
        $sql = "SELECT * FROM facebook_comments
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


            $sql = "INSERT INTO facebook_comments (link)
                    VALUES (:link)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':link', $this->link, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }
    public function validate($id = null){
        if(empty($this->link)){
            $this->errors[] = "Введите 'Ссылку' комментария";
        }

    }
    public function saveChanges($id) 
    {
        $this->validate($id);

        if(empty($this->errors)) {

            $sql = "UPDATE facebook_comments
                    SET link = :link 
                    WHERE id = :id";
                    

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':link', $this->link, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

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
        
        $sql = "DELETE FROM facebook_comments
                WHERE id = :id";
        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id",$id,PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        return $stmt->execute();
        
    }
    
}