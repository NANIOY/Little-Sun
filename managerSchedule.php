<?php
include_once (__DIR__ . '/includes/auth.inc.php');
include_once (__DIR__ . '/classes/TimeOff.php');
include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/classes/Calender.php');

requireManager();


?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Schedule</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/workerschedule.css">
    <link rel="stylesheet" href="css/pagestyles/calendar.css">

</head>

<body>
    <?php include_once ("./includes/managerNav.inc.php"); ?>

    <div class="workers">
        <div class="workers__header">
            <h3>Workers calendar</h3>
        </div>

        <div class="workers__calendar">
                <?php
                    $calendar = new Calendar();

                    echo $calendar->show();
                ?>
           
        </div>

    </div>
</body>

</html>