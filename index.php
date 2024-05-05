<?php
    include_once (__DIR__ . '/classes/User.php');
    
    session_start();
    if(isset($_SESSION['user'])){
        //echo 'Welcome ' . $_SESSION['user']['first_name'];
    

    }else{
        header('Location: login.php');
    }

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LITTLESUN☀️</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="https://use.typekit.net/qgt5qis.css">
</head>
<body>
    <header>
        <?php include_once ("./includes/nav.inc.php"); ?>

    </header>
    <main>

       
    </main>
    
</body>
</html>