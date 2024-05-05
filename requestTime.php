<?php
include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/classes/Location.php');
include_once (__DIR__ . '/classes/TimeOff.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireWorker();

$worker = User::getById($_SESSION['user']['id']);

if (!empty($_POST)) {
    try {

        $timeOff = new TimeOff();
        $timeOff->setStartDate($_POST['startDate']);
        $timeOff->setEndDate($_POST['endDate']);
        $timeOff->setReason($_POST['reason']);
        $timeOff->save();
        
        header('Location: workerSchedule.php');
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
    <title>Little Sun | Request Time Off</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
    <style>

    </style>
</head>

<?php include_once ("./includes/workerNav.inc.php"); ?>

<body>
    <div class="formContainer">
        <h4 class="formContainer__title">Request time off</h4>

        <form action="" method="post" enctype="multipart/form-data" class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="start_date" class="text-reg-s">Start date</label>
                <input type="datetime-local" id="start_date" name="startDate" required />
            </div>

            <div class="formContainer__form__field">
                <label for="end_date" class="text-reg-s">End date</label>
                <input type="datetime-local" id="end_date" name="endDate" required />
            </div>

            <div class="formContainer__form__field">
                <label for="reason" class="text-reg-s">Reason:</label>
                <input type="text" id="reason" name="reason" class="formContainer__form__field__input text-reg-normal"
                    required placeholder="Enter reason for time off">
            </div>

            <button type="submit" class="formContainer__form__button button--primary">Submit</button>
        </form>

    </div>
</body>

</html>