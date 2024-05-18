<?php

abstract class Db
{
    private static $conn;

    public static function getInstance()
    {
        include_once __DIR__ . '/../settings/db.php';

        if (self::$conn === null) {
            $db = SETTINGS['db'];

            $dsn = 'mysql:host=' . $db['host'] . ';port=' . $db['port'] . ';dbname=' . $db['database'] . ';charset=utf8mb4';

            $options = [
                PDO::MYSQL_ATTR_SSL_CA => $db['ssl']['ca'],
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$conn = new PDO($dsn, $db['user'], $db['password'], $options);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }

        return self::$conn;
    }
}
