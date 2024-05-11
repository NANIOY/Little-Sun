<?php
include_once (__DIR__ . '/includes/auth.inc.php');
include_once (__DIR__ . '/classes/Attendance.php');
include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/classes/Task.php');

requireWorker();
$userId = $_SESSION['user']['id'];
$worker = User::getById($userId);
$status = Attendance::getCurrentStatus($userId);
$workerTasks = Task::getTasksByWorkerId($userId);

if (preg_match("/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/", $status['message'], $matches)) {
    $dateTime = new DateTime($matches[1]);
    $formattedTime = $dateTime->format('H:i');
    $status['message'] = str_replace($matches[1], $formattedTime, $status['message']);
}

if (isset($_POST['clockIn'])) {
    Attendance::clockIn($userId);
    $status = Attendance::getCurrentStatus($userId);
    if (preg_match("/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/", $status['message'], $matches)) {
        $dateTime = new DateTime($matches[1]);
        $formattedTime = $dateTime->format('H:i');
        $status['message'] = str_replace($matches[1], $formattedTime, $status['message']);
    }
}

if (isset($_POST['clockOut'])) {
    Attendance::clockOut($userId);
    $status = Attendance::getCurrentStatus($userId);
    if (preg_match("/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/", $status['message'], $matches)) {
        $dateTime = new DateTime($matches[1]);
        $formattedTime = $dateTime->format('H:i');
        $status['message'] = str_replace($matches[1], $formattedTime, $status['message']);
    }
}

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | <?= htmlspecialchars($worker['first_name']) ?>'s Dashboard</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/workerDashboard.css">
    <link rel="stylesheet" href="css/pagestyles/tasks.css">
</head>

<body>
    <?php include_once ("./includes/workerNav.inc.php"); ?>

    <div class="dashboard">
        <div class="dashboard__section--top">
            <h2>Welcome, <?= htmlspecialchars($worker['first_name']); ?></h2>
            <div>
                <?php if (!$status['clocked_in']): ?>
                    <form method="post">
                        <button type="submit" class="button--primary" name="clockIn">Clock In</button>
                    </form>
                <?php else: ?>
                    <form method="post">
                        <button type="submit" class="button--primary" name="clockOut">Clock Out</button>
                    </form>
                <?php endif; ?>
                <p class="dashboard__section__msg text-reg-normal"><?= $status['message']; ?></p>
            </div>
        </div>
        <div class="dashboard__section">
            <h3>Your Tasks</h3>
            <div class="dashboard__section__tasklist">
                <?php if (!empty($workerTasks)): ?>
                    <?php foreach ($workerTasks as $task): ?>
                        <div class="tasklist__tag" style="background-color: <?= htmlspecialchars($task['color']); ?>">
                            <?= htmlspecialchars($task['title']); ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No tasks assigned.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>