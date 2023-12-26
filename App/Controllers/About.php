<?php

namespace App\Controllers;

use App\Models\Customer;
use Core\Controller;
use \Core\View;
use \App\Request;
use \App\Models\CustomerMessage;

class About extends Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('About/index.html');
    }


    public function contactAction()
    {
        View::renderTemplate('About/contact.html');
    }

    /**
     * Send message
     *
     * @return void
     */
    public function sendAction()
    {
        if(!Request::isPost()){
            throw new \Exception("POST method is not allowed");
        }

        $errors = $this->validate($_POST);

   /*     if(Config::ENABLE_PRODUCTION){
            if(empty($this->{'g-recaptcha-response'})){
                // Флажок рекапчи не был отмечен
                $errors['recaptcha_failed'] = "Подтвердите, что вы не робот!";
            }else{

                $verify = curl_init();
                curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify?secret=".Config::SECRET_KEY."&response=".$this->{'g-recaptcha-response'});
                curl_setopt($verify, CURLOPT_POST, true);
                curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($verify);
                $response = json_decode($response,true);
                if($response['success'] != true){
                    // ответ рекапчи не вернул success = true
                    $errors['recaptcha_failed'] = "Ответ рекапчи не вернул успех";
                }
            }
        }*/


        if(count($errors) > 0) {
            if(Request::isAjax()){
                $response = [
                    "errors" => $errors
                ];
                echo json_encode($response);

            }else{
                View::renderTemplate('About/contact.html',[
                    "errors" => $errors,
                    "post_data" => $_POST
                ]);
            }
            return;
        }

        $message = new CustomerMessage($_POST);

        $customer =  Customer::findByPhone($_POST['phone']);
        if(!$customer){
            $customer = new Customer($_POST);
            $customer->save();
        }
        $message->customer()->associate($customer);
        $message->save();

        echo json_encode([
            'errors' => []
        ]);

/*        $post_data = [
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
            "message" => [
                "value" => $this->message ?? "",
                "description" => "Сообщение",
                "emoji"=>"\xF0\x9F\x93\xA8"
            ]
        ];

        $telegram = new Telegram(Config::BOT_TOKEN,Config::CHAT_ID);
        $telegram->send("Сообщение",$post_data);*/



    }


    public function validate($data = []): array
    {
        $errors = [];

        if(empty($data['name'])) {
            $errors[] = 'Введите имя';
        }

        if(!preg_match('/^\+?3?8?\(?0\d{2}\)?\-?\d{3}\-?\d{2}\d{2}$/',$data['phone'])) {
            $errors[] = 'Введите телефон в формате +380xxxxxxx';
        }

        return $errors;
    }


}