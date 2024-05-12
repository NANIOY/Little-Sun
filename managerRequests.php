<?php
    include_once (__DIR__ . '/includes/auth.inc.php');
    include_once (__DIR__ . '/classes/TimeOff.php');
    include_once (__DIR__ . '/classes/User.php');

    $timeOffTasks = TimeOff::getAll();

    $worker = User::getAll();

    requireManager();
    $locationId = $_SESSION['user']['location_id'];

    $timeOffRequests = TimeOff::getAllForLocation($locationId);


    function getStatus($approvedCode)
    {
        switch ($approvedCode) {
            case 0:
                return 'Pending';
            case 1:
                return 'Declined';
            case 2:
                return 'Approved';
            default:
                return 'Unknown';
        }
    }

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Time Off Requests</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/workerschedule.css">
</head>

<body>
    <?php include_once ("./includes/managerNav.inc.php"); ?>

    <div class="workers">
        <h3>All Time Off Requests</h3>

        <div class="workers__list">
            <?php if (!empty($timeOffRequests)): ?>
                <div class="workers__list__timeoff">
                    <?php foreach ($timeOffRequests as $request): ?>
                        <a href="managerApprove.php?id=<?= $request['id'] ?>" class="workers__list__timeoff__request_link">
                            <div class="workers__list__timeoff__request">
                                <strong>Employee:</strong>
                                <?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?><br>
                                <strong>Date:</strong> <?= date("Y-m-d H:i", strtotime($request['startDate'])) ?> to
                                <?= date("Y-m-d H:i", strtotime($request['endDate'])) ?><br>
                                <strong>Reason:</strong> <?= htmlspecialchars($request['reason']) ?><br>
                                <strong>Status:</strong> <?= getStatus($request['approved']) ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No time off requests found.</p>
            <?php endif; ?>
        </div>
    </div>
    
</body>

</html>