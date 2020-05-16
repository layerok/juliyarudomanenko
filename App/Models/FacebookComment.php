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
    /**
     * Get all the facebook comment as an associative array
     *
     * @return array
     */
    public function getAll()
    {
        $sql ="SELECT * FROM facebook_comments";

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
    
}