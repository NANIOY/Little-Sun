<?php

    include_once (__DIR__ . '/classes/User.php');
    include_once (__DIR__ . '/classes/Admin.php');
    include_once (__DIR__ . '/includes/auth.inc.php');

    requireAdmin();
    
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
    <title>Little Sun | Dashboard</title>
    <link rel="stylesheet" href="css/global.css">
</head>

<body>
    <?php include_once ("./includes/adminNav.inc.php"); ?>


</body>

</html>