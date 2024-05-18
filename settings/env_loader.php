<?php

if (!function_exists('loadEnv')) {
    function loadEnv()
    {

        error_log('env_loader.php is loaded');

        error_log('DB_USER: ' . getenv('DB_USER'));
        error_log('DB_PASSWORD: ' . getenv('DB_PASSWORD'));
        error_log('DB_HOST: ' . getenv('DB_HOST'));
        error_log('DB_PORT: ' . getenv('DB_PORT'));
        error_log('DB_DATABASE: ' . getenv('DB_DATABASE'));
    }

    loadEnv();
}
