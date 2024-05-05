<?php
    include_once (__DIR__ . '/includes/auth.inc.php');
    include_once (__DIR__ . '/classes/TimeOff.php');
    include_once (__DIR__ . '/classes/User.php');

    requireManager();

    $timeOffTasks = TimeOff::getAll();

    $worker = User::getAll();



?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Tasks</title>
    <link rel="stylesheet" href="css/global.css">
    
    <link rel="stylesheet" href="css/pagestyles/workers.css">
</head>

<body>
    <?php include_once ("./includes/managerNav.inc.php"); ?>

    <div class="workers">
        <div class="workers__header">
            <h3>All Tasks</h3>
        </div>

        <div class="workercards">
            <?php foreach ($timeOffTasks as $timeOffTask):
                $timeOffTask = TimeOff::getRequestByWorkerId($workers['id']);?>
                   
                <a href="timeOff.php?id=<?php echo $timeOffTask['id']; ?>" class="workercard">
                    
                    <div class="workercard__info">
                        <div class="text-bold-normal">
                            <?php echo $worker['first_name'] . ' ' . $worker['last_name']; ?>
                        </div>
                    </div>

                    <div class="workercard__info">
                        <div class="text-bold-normal">
                            <?php echo $timeOffTask['startDate'] . ' ' . $timeOffTask['endDate']; ?>
                        </div>
                        <div class="text-bold-normal">
                            <?php echo $timeOffTask['reason']; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>

        </div>
    </div>
    
</body>

</html>