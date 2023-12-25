<?php

namespace App\Models;

use PDO;
use \App\Config;
use \Verot\Upload\Upload;

/**
 * post model
 *
 * PHP version 7.0
 */
class Post extends \Core\Model
{
    /**
     * Error messages
     * 
     * @var array
     */
    public $errors = [];

    public $path = '/img/posts/';

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

    public static function getAll(){
        $sql = "SELECT * FROM posts ORDER BY id DESC";

        $db = static::getDB();
        $stmt = $db->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->fetchAll();
    }

    /**
     * Get post by id as an associative array
     *
     * @return array
     */
    public function getOne($id)
    {
        $sql = "SELECT * FROM posts
                WHERE id = :id";
        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id",$id,PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }


    /**
     * Validate current property values, adding validation error messages to the errors array property
     * 
     * @return void
     */
    public function validate($id = null)
    {
        // title
        if($this->title == '') {
            $this->errors[] = 'Введите заголовок поста';
        }
        

        // slug
        if($this->slug == '') {
            $this->errors[] = 'Введите slug';
        }
        if(self::slugExists($this->slug, $this->id ?? null) ){
            $this->errors[] = 'Пост с таким слагом существует';
        }

        // metaTitle
        if(empty($this->meta_title)) {
            $this->meta_title = $this->title ;
        }
        // metaDescription
        if(empty($this->meta_description)) {
            $this->meta_description = $this->title;
        }

        // content
        if($this->content == '') {
            $this->errors[] = 'Введите содержимое поста';
        }
        
        // image
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
     * Save the appointment model with the current property values
     * 
     * @return
     */
    public function save() 
    {
        $this->validate();

        if(empty($this->errors)) {
            
            $sql = 'INSERT INTO posts (title, slug, content, meta_title, meta_description, image)
                    VALUES (:title, :slug, :content, :meta_title, :meta_description, :image)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':slug', $this->slug, PDO::PARAM_STR);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);
            $stmt->bindValue(':meta_title', $this->meta_title, PDO::PARAM_STR);
            $stmt->bindValue(':meta_description', $this->meta_description, PDO::PARAM_STR);
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
        $this->content = htmlspecialchars($this->content);
        if(empty($this->errors)) {

            
            $sql = "UPDATE posts 
                    SET title = :title, 
                        slug = :slug, 
                        content = :content,
                        meta_title = :meta_title,
                        meta_description = :meta_description,
                        image = :image
                    WHERE id = :id";
                    

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':slug', $this->slug, PDO::PARAM_STR);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);
            $stmt->bindValue(':image', $this->image_name, PDO::PARAM_STR);
            $stmt->bindValue(':meta_title', $this->meta_title, PDO::PARAM_STR);
            $stmt->bindValue(':meta_description', $this->meta_description, PDO::PARAM_STR);
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
        $record= $this->getOne($id);
        $this->deleteFile($this->path.$record->image);
        $sql = "DELETE FROM posts
                WHERE id = :id";
        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id",$id,PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        return $stmt->execute();
        
    }
        /**
     * See if a post record already exists with the specified slug
     * 
     * @param string $slug slug address to search for
     * 
     * @return boolean True if a record already exists with the specidied slug, false otherwise
     * @param string $ignore_id Return false anyway if the record found has this ID
     */
    public static function slugExists($slug, $ignore_id = null) 
    {
        $user = static::findBySlug($slug);

        if($user) {
            if($user->id != $ignore_id) {
                return true;
            }
        }
        return;
    }

    /**
     * Find a post model by slug
     * 
     * @param string $slug slug to search for
     * 
     * @return mixed Post object if found, false otherwise
     */
    public static function findBySlug($slug)
    {
        $sql = 'SELECT * FROM posts WHERE slug = :slug';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    

    
    
    
}