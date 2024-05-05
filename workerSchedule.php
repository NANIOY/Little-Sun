<?php
    include_once (__DIR__ . '/includes/auth.inc.php');

    requireWorker();

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Schedule</title>
    <link rel="stylesheet" href="css/global.css">
</head>

<body>
    <?php include_once ("./includes/workerNav.inc.php"); ?>

    <div class="workers">
        <div class="workers__header">
            <h3>My Schedule</h3>
            <button onclick="window.location.href='requestTime.php'" class="button--primary">Request time off</button>
        </div>
    
</body>

</html>

