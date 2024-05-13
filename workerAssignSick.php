<?php

    include_once (__DIR__ . '/classes/User.php');
    include_once (__DIR__ . '/classes/Task.php');
    include_once (__DIR__ . '/includes/auth.inc.php');

    requireWorker();

    /* get date  */
    if (!isset($_GET['date'])) {
        echo 'Date not provided.';
        exit();
    }
    
    $date = $_GET['date'];

    $worker = User::getById($_SESSION['user']['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign sick on day</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
</head>
<body>
    <?php include_once ("./includes/workerNav.inc.php"); ?>

    <div class="formContainer">
        <h4 class="formContainer__title">Assign sick on the day: <?php echo $date; ?></h4>
        <form action="workerAssignSick.php?date=<?php echo $date; ?>" method="post" class="formContainer__form"
            id="assignForm">

            <!-- Worker name set from session-->
            <div class="formContainer__form__field">
                <label for="user_id" class="text-reg-s">Worker:</label>
                <?php echo $worker['first_name'] . ' ' . $worker['last_name']; ?>
            </div>

            <!-- Sick reason -->
            <div class="formContainer__form__field">
                <label for="reason" class="text-reg-s">Sick reason:</label>
                <input type="text" id="reason" name="reason" required />
            </div>
      
          
            <!-- Start-End time sick  -->

            <div class="formContainer__form__field">
                <label for="start_time">Start Time:</label>
                <select id="start_time" name="start_time" class="formContainer__form__field__input" required>
                    <?php
                    $startTime = strtotime('07:00');
                    for ($i = 0; $i < 12; $i++) {
                        $time = date('H:i', $startTime + $i * 3600);
                        echo "<option value=\"$time\">$time</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="formContainer__form__field">
                <label for="end_time">End Time:</label>
                <select id="end_time" name="end_time" class="formContainer__form__field__input" required>
                    <?php
                    $endTime = strtotime('07:00') + 3600;
                    for ($i = 0; $i < 12; $i++) {
                        $time = date('H:i', $endTime + $i * 3600);
                        echo "<option value=\"$time\">$time</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="formContainer__form__button button--primary">Enter Sick</button>
        </form>
    </div>
</body>
</html>