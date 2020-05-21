<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
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
    const DB_NAME = 'juliya_rudomanenko_db';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';
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
    const SHOW_ERRORS = true;

    /**
     * enable or disable production mode
     * @var boolean
     */
    const ENABLE_PRODUCTION = false;
}
