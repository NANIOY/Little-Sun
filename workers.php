<?php

include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireManager();

$workers = User::getAllWorkers();

include_once (__DIR__ . '/classes/Manager.php');




?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Workers</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/workers.css">
    <link rel="stylesheet" href="css/pagestyles/managers.css">
</head>

<?php include_once ("./includes/managerNav.inc.php"); ?>

<body>
    <div class="workers">
        <div class="workers__header">
            <h3>All Workers</h3>
            <button onclick="window.location.href='addWorker.php'" class="button--primary">Add worker</button>
        </div>

        <div class="workercards">
            <?php foreach ($workers as $worker): ?>
                <a href="profileWorker.php?id=<?php echo $worker['id']; ?>" class="workercard">
                    <img src="<?php echo $worker['profile_img']; ?>" alt="Profile Image"
                        class="workercard__img profileimg">
                    <div class="workercard__info">
                        <div class="text-bold-normal">
                            <?php echo $worker['first_name'] . ' ' . $worker['last_name']; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>


</html>