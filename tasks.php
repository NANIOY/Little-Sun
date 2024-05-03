<?php
include_once (__DIR__ . '/classes/Task.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireAdmin();

$tasks = Task::getAll();

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Tasks</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/managers.css">
    <link rel="stylesheet" href="css/pagestyles/tasks.css">
</head>

<?php include_once ("./includes/adminNav.inc.php"); ?>

<body>
    <div class="managers">
        <div class="managers__header">
            <h3>All Tasks</h3>
            <button onclick="window.location.href='addTask.php'" class="button--primary">Add task</button>
        </div>

        <div class="tasklist">
            <?php foreach ($tasks as $task): ?>
                <div class="tasklist__item">
                    <div class="tasklist__item__title text-bold-normal">
                        <?php echo $task['title']; ?>
                    </div>
                    <div class="tasklist__item__color"
                        style="background-color: <?php echo htmlspecialchars($task['color']); ?>;"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>