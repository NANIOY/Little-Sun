<?php
include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/classes/Task.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireWorker();

$workerId = $_SESSION['user']['id'];
$workerTasks = Task::getTasksByWorkerId($workerId);
$worker = User::getById($workerId);

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($worker['first_name']) ?>'s Tasks | Little Sun</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/tasks.css">
    <link rel="stylesheet" href="css/pagestyles/workers.css">
</head>

<?php include_once ("./includes/workerNav.inc.php"); ?>

<body>
    <div class="workers">
        <div class="workers__header">
            <h3><?= htmlspecialchars($worker['first_name']) ?>'s Tasks</h3>
        </div>

        <div class="tasklist">
            <?php if (!empty($workerTasks)): ?>
                <?php foreach ($workerTasks as $task): ?>
                    <div class="tasklist__tag text-bold-s" style="background-color: <?= htmlspecialchars($task['color']); ?>">
                        <?= htmlspecialchars($task['title']); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tasks assigned.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>