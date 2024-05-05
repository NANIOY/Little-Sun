<?php
include_once (__DIR__ . '/includes/auth.inc.php');
include_once (__DIR__ . '/classes/TimeOff.php');
include_once (__DIR__ . '/classes/User.php');

requireManager();
$locationId = $_SESSION['user']['location_id'];

$timeOffRequests = TimeOff::getAllForLocation($locationId);

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Schedule</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/workerschedule.css">
</head>

<body>
    <?php include_once ("./includes/managerNav.inc.php"); ?>

    <div class="workers">
        <div class="workers__header">
            <h3>Worker Time Off Requests</h3>
        </div>

        <div class="workers__list">
            <?php if (!empty($timeOffRequests)): ?>
                <div class="workers__list__timeoff">
                    <?php foreach ($timeOffRequests as $request): ?>
                        <div class="workers__list__timeoff__request">
                            <strong>Employee:
                            </strong><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?><br>
                            <strong>Date: </strong><?= date("Y-m-d H:i", strtotime($request['startDate'])) ?> to
                            <?= date("Y-m-d H:i", strtotime($request['endDate'])) ?><br>
                            <strong>Reason: </strong><?= htmlspecialchars($request['reason']) ?><br>
                            <strong>Status: </strong>
                            <?= isset($request['approved']) ? ($request['approved'] ? 'Approved' : 'Pending') : 'Unknown' ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No time off requests found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>