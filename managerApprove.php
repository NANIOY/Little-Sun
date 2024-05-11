<?php
include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/classes/Location.php');
include_once (__DIR__ . '/classes/TimeOff.php');
include_once (__DIR__ . '/includes/auth.inc.php');


requireManager();

if (isset($_GET['id'])) {
    $timeOffRequest = TimeOff::getById($_GET['id']);
} else {
    header('Location: managerSchedule.php');
    exit();
}

// added
$comma_separated = implode(",", $timeOffRequest);

$user = User::getById($timeOffRequest['userId']);

if (isset($user['first_name'])){
    $user = User::getById($timeOffRequest['userId']);// NOT WORKING - $user is empty
    $timeOffRequest['first_name'] = $user['first_name'];
    $timeOffRequest['last_name'] = $user['last_name'];
}else{
    $timeOffRequest['first_name'] = 'Unknown firstname';
    $timeOffRequest['last_name'] = 'Unknown lastname';
}


// added - end

echo $comma_separated;

if (!empty($_POST)) {
    try {
        $status = $_POST['status'];
        $declineReason = $_POST['declineReason'] ?? '';

        $timeOffRequest->setApproved($status);
        if ($status == 1) {
            $timeOffRequest->setDeclineReason($declineReason);
        }
        $timeOffRequest->update();

        header('Location: managerSchedule.php');
        exit();
    } catch (Throwable $th) {
        $error = $th->getMessage();
        echo "Error: " . $error;
    }
}

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Approve Time Off</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
</head>

<?php include_once ("./includes/managerNav.inc.php"); ?>

<body>
    <?php include_once ("./includes/managerNav.inc.php"); ?>

    <div class="formContainer">
        <h4 class="formContainer__title">Handle request</h4>

        <div class="requestDetails">
            <h5>Request Details</h5>
            <p><strong>Employee:</strong>
                <?= htmlspecialchars($timeOffRequest['first_name'] . ' ' . $timeOffRequest['last_name']) ?></p>
            <p><strong>Date Range:</strong> <?= date("Y-m-d H:i", strtotime($timeOffRequest['startDate'])) ?> to
                <?= date("Y-m-d H:i", strtotime($timeOffRequest['endDate'])) ?></p>
            <p><strong>Reason for Time Off:</strong> <?= htmlspecialchars($timeOffRequest['reason']) ?></p>
            <p><strong>Current Status:</strong> <?= getStatus($timeOffRequest['approved']) ?></p>
        </div>

        <form action="" method="post" class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="status">Update Status:</label>
                <select id="status" name="status" class="formContainer__form__field__input">
                    <option value="2" <?= $timeOffRequest['approved'] == 2 ? 'selected' : '' ?>>Approve</option>
                    <option value="1" <?= $timeOffRequest['approved'] == 1 ? 'selected' : '' ?>>Decline</option>
                    <option value="0" <?= $timeOffRequest['approved'] == 0 ? 'selected' : '' ?>>Pending</option>
                </select>
            </div>

            <div class="formContainer__form__field">
                <label for="declineReason">Reason for Decline (if applicable):</label>
                <input type="text" id="declineReason" name="declineReason" class="formContainer__form__field__input"
                    placeholder="Enter reason if declined"
                    value="<?= htmlspecialchars($timeOffRequest['decline_reason'] ?? '') ?>">
            </div>

            <button type="submit" class="formContainer__form__button button--primary">Update Request</button>
        </form>
    </div>
</body>

</html>