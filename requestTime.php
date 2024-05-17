<?php
include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/classes/Location.php');
include_once (__DIR__ . '/classes/TimeOff.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireWorker();

$worker = User::getById($_SESSION['user']['id']);
$error = '';

if (!empty($_POST)) {
    try {
        $dates = explode(',', $_POST['dates']);
        $reason = $_POST['reason'];

        foreach ($dates as $date) {
            $existingTimeOff = TimeOff::getByDate($worker['id'], $date);
            if (!empty($existingTimeOff)) {
                throw new Exception("Can not ask time off multiple times for: $date");
            }
        }

        foreach ($dates as $date) {
            $timeOff = new TimeOff();
            $timeOff->setStartDate($date);
            $timeOff->setEndDate($date);
            $timeOff->setReason($reason);
            $timeOff->save();
        }

        header('Location: workerSchedule.php');
        exit();
    } catch (Throwable $th) {
        $error = $th->getMessage();
    }
}

$dates = isset($_GET['dates']) ? $_GET['dates'] : '';

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Request Time Off</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
</head>

<?php include_once ("./includes/workerNav.inc.php"); ?>

<body>
    <div class="formContainer">
        <h4 class="formContainer__title">Request time off for the days: <?php echo htmlspecialchars($dates); ?></h4>
        <?php if ($error): ?>
            <div class="formContainer__error">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data" class="formContainer__form">
            <input type="hidden" name="dates" value="<?php echo htmlspecialchars($dates); ?>" />

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