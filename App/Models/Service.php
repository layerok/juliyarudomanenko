<?php

namespace App\Models;

use PDO;


/**
 * Example user model
 *
 * PHP version 7.0
 */
class Service extends \Core\Model
{
    public $path = "/img/services/";
    public $currency = " грн.";
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
    public function getService($id)
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
    
}