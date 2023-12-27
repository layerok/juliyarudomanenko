<?php

namespace App;


class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'juliya_rudomanenko';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = "";
    /**
     * telegram bot token
     * @var string
     */
    const BOT_TOKEN = "";
    /**
     * telegram chat_id 
     * @var string
     */
    const CHAT_ID = "";
     /**
     * recaptcha site key 
     * @var string
     */
    const SITE_KEY = "";
     /**
     * recaptcha secret key 
     * @var string
     */
    const SECRET_KEY = "";

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = false;

    /**
     * enable or disable production mode
     * @var boolean
     */
    const ENABLE_PRODUCTION = false;

    const LOCALE = 'uk';

    const FALLBACK_LOCALE = 'ru';
}
