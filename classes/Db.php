<?php

abstract class Db
{
    private static $conn;

    public static function getInstance()
    {
        require __DIR__ . '/../settings/db.php';

       
    }
}
