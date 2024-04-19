<?php

abstract class Db
{
    private static $conn;

    public static function getInstance()
    {
        include_once __DIR__ . '/../settings/db.php';

        if (self::$conn === null) {
            self::$conn = new PDO('mysql:host=' . SETTINGS['db']['host'] . ';dbname=' . SETTINGS['db']['database'], SETTINGS['db']['user'], SETTINGS['db']['password']);
            return self::$conn;
        } else {
            return self::$conn;
        }
    }
}
