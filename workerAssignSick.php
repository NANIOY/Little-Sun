<?php

include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireWorker();

/* get dates  */
if (!isset($_GET['dates'])) {
    echo 'Dates not provided.';
    exit();
}

$dates = explode(',', $_GET['dates']);
$worker = User::getById($_SESSION['user']['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = $_POST['reason'];
    foreach ($dates as $date) {
        User::assignSick($worker['id'], $date, $reason);
    }
    header("Location: workerSchedule.php");
    exit();
}

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign sick on days</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
</head>

<body>
    <?php include_once ("./includes/workerNav.inc.php"); ?>

    <div class="formContainer">
        <h4 class="formContainer__title">Assign sick on the days: <?php echo htmlspecialchars(implode(', ', $dates)); ?></h4>
        <form action="workerAssignSick.php?dates=<?php echo htmlspecialchars(implode(',', $dates)); ?>" method="post"
            class="formContainer__form" id="assignForm">

            <div class="formContainer__form__field">
                <label for="reason" class="text-reg-s">Sick reason:</label>
                <input type="text" id="reason" name="reason" required />
            </div>

            <button type="submit" class="formContainer__form__button button--primary">Enter your sickness</button>
        </form>
    </div>
</body>

</html>
