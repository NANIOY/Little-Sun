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

if (isset($_POST['clockIn'])) {
    Attendance::clockIn($userId);
    $status = Attendance::getCurrentStatus($userId);
}

if (isset($_POST['clockOut'])) {
    Attendance::clockOut($userId);
    $status = Attendance::getCurrentStatus($userId);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($worker['first_name']) ?>'s Dashboard | Little Sun</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/tasks.css">
    <link rel="stylesheet" href="css/pagestyles/workers.css">
</head>

<body>
    <?php include_once ("./includes/workerNav.inc.php"); ?>

    <div class="dashboard">
        <div class="dashboard-section">
            <h2>Welcome, <?= htmlspecialchars($worker['first_name']); ?></h2>
            <div class="attendance-controls">
                <?php if (!$status['clocked_in']): ?>
                    <form method="post">
                        <button type="submit" name="clockIn">Clock In</button>
                    </form>
                <?php else: ?>
                    <form method="post">
                        <button type="submit" name="clockOut">Clock Out</button>
                    </form>
                <?php endif; ?>
                <p id="attendanceStatus"><?= $status['message']; ?></p>
            </div>
        </div>

        <div class="dashboard-section">
            <h3>Your Tasks</h3>
            <div class="tasklist">
                <?php if (!empty($workerTasks)): ?>
                    <?php foreach ($workerTasks as $task): ?>
                        <div class="tasklist__tag text-bold-s"
                            style="background-color: <?= htmlspecialchars($task['color']); ?>">
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