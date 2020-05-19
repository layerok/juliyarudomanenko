<?php
namespace App;

//** Работает только на сервере https */

class Telegram {
    public $token;
    public $chat_id;
    public $headline;
    public $message;

    public function __construct($token, $chat_id){
        $this->token = $token;
        $this->chat_id = $chat_id;
    }

    public function send($headline, $message){
        $this->headline = $headline;
        $this->message = $message;
        // send message
        $txt="\xF0\x9F\x93\x83 <b>$this->headline</b> %0A %0A";
        foreach($this->message as $key => $value){
            $txt .= $value['emoji']." <b>".$value['description']."</b>: ".$value['value'].".%0A";
        }
        if(Config::ENABLE_PRODUCTION){
            return $sendToTelegram = fopen("https://api.telegram.org/bot{$this->token}/sendMessage?chat_id={$this->chat_id}&parse_mode=html&text={$txt}","r"); 
        }
        
        
        
    }
}
?>