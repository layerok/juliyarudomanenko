<?php

namespace App;




class Upload {
    public $errors = [];
    public $file;
    public $fileTmpName;
    

    public function __construct($file){
        $this->file = $file;
    }
    public function getRandomFileName($path)
    {
        $path = $path ? $path . '/' : '';
        do {
            $name = md5(microtime() . rand(0, 9999));
            $file = $path . $name;
        } while (file_exists($file));
        
        return $name;
    }

    public function validate(){
        // Если в $_FILES существует "image" и она не NULL
        if (isset($this->file)) {
            $image = $this->file;
            // Получаем нужные элементы массива "image"
            $this->fileTmpName = $this->file['tmp_name'];
            $errorCode = $this->file['error'];
            // Проверим на ошибки
            if ($errorCode !== UPLOAD_ERR_OK || !is_uploaded_file($this->fileTmpName)) {
                // Массив с названиями ошибок
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE   => 'Размер файла превысил значение upload_max_filesize в конфигурации PHP.',
                    UPLOAD_ERR_FORM_SIZE  => 'Размер загружаемого файла превысил значение MAX_FILE_SIZE в HTML-форме.',
                    UPLOAD_ERR_PARTIAL    => 'Загружаемый файл был получен только частично.',
                    UPLOAD_ERR_NO_FILE    => 'Файл не был загружен.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка.',
                    UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск.',
                    UPLOAD_ERR_EXTENSION  => 'PHP-расширение остановило загрузку файла.',
                ];
                
                // Зададим неизвестную ошибку
                $unknownMessage = 'При загрузке файла произошла неизвестная ошибка.';
                // Если в массиве нет кода ошибки, скажем, что ошибка неизвестна
                $this->errors[] = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : $unknownMessage;
                return;
            } 
            
            // Создадим ресурс FileInfo
            $fi = finfo_open(FILEINFO_MIME_TYPE);
            
            // Получим MIME-тип
            $mime = (string) finfo_file($fi, $this->fileTmpName);
            
            // Проверим ключевое слово image (image/jpeg, image/png и т. д.)
            if (strpos($mime, 'image') === false) $this->errors[] = 'Можно загружать только изображения.';
            
            
        }else{
            $this->errors[] = 'Файл не был загружен.';
        }
    }
    public function save($path){
        $this->validate();

        if(empty($this->errors)){
            $image = getimagesize($this->fileTmpName);

            // Сгенерируем новое имя файла через функцию getRandomFileName()
            $name = $this->getRandomFileName($this->fileTmpName);

            // Сгенерируем расширение файла на основе типа картинки
            $extension = image_type_to_extension($image[2]);
            
            // Сократим .jpeg до .jpg
            $format = str_replace('jpeg', 'jpg', $extension);
            
            // Переместим картинку с новым именем и расширением в папку /upload
            if (!move_uploaded_file($this->fileTmpName, "C:/xampp/htdocs/juliyarudomanenko/public".$path . $name . $format)) {
            $this->errors = 'При записи изображения на диск произошла ошибка.';
            return false;
            }
            $this->image_name = $name . $format;
            return true;
            
            
        }
        return false;
    }
    public static function delete($path = null){
        unlink("C:/xampp/htdocs/juliyarudomanenko/public".$path);
    }
}
