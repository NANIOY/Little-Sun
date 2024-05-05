<?php
include_once (__DIR__ . '/includes/auth.inc.php');
include_once (__DIR__ . '/classes/TimeOff.php');

requireWorker();
$user_id = $_SESSION['user']['id'];

$timesOff = TimeOff::getAllForUser($user_id);

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Schedule</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/workers.css">
</head>

<body>
    <?php include_once ("./includes/workerNav.inc.php"); ?>

    <div class="workers">
        <div class="workers__header">
            <h3>My Schedule</h3>
            <button onclick="window.location.href='requestTime.php'" class="button--primary">Request time off</button>
        </div>

        <div class="workers__list">
            <?php
            $timeOffRequests = TimeOff::getAllForUser($_SESSION['user']['id']);
            if (!empty($timeOffRequests)): ?>
                <ul>
                    <?php foreach ($timeOffRequests as $request): ?>
                        <li>
                            <strong>Date: </strong><?= date("Y-m-d H:i", strtotime($request['startDate'])) ?> to
                            <?= date("Y-m-d H:i", strtotime($request['endDate'])) ?><br>
                            <strong>Reason: </strong><?= htmlspecialchars($request['reason']) ?><br>
                            <strong>Status:
                            </strong><?= isset($request['approved']) && $request['approved'] ? 'Approved' : 'Pending' ?>
                        </li>
                    <?php endforeach; ?>

                </ul>
            <?php else: ?>
                <p>No time off requests found.</p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>