<?php
include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/classes/Task.php');
include_once (__DIR__ . '/classes/ScheduleManager.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireManager();

if (!isset($_GET['date'])) {
    echo 'Date not provided.';
    exit();
}
$date = $_GET['date'];

if (!isset($_SESSION['user']['location_id'])) {
    echo 'Manager hub location not set.';
    exit();
}
$locationId = $_SESSION['user']['location_id'];

$tasks = Task::getAll();
$workers = User::getAllWorkers($locationId);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'];
    $taskId = $_POST['task_id'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];

    $response = ScheduleManager::assignSchedule($userId, $taskId, $startTime, $endTime, $date, $locationId);

    if ($response['success']) {
        header("Location: managerDashboard.php?success=1");
    } else {
        $errorMsg = $response['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Task</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
</head>

<body>
    <?php include_once ("./includes/managerNav.inc.php"); ?>
    <div class="formContainer">
        <h4 class="formContainer__title">Assign Task for Date: <?php echo $date; ?></h4>
        <?php if (isset($errorMsg)): ?>
            <p class="error"><?php echo $errorMsg; ?></p>
        <?php endif; ?>
        <form action="managerAssign.php?date=<?php echo $date; ?>" method="post" class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="user_id">User:</label>
                <select id="user_id" name="user_id" class="formContainer__form__field__input">
                    <?php foreach ($workers as $worker): ?>
                        <option value="<?php echo $worker['id']; ?>">
                            <?php echo $worker['first_name'] . ' ' . $worker['last_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="formContainer__form__field">
                <label for="task_id">Task:</label>
                <select id="task_id" name="task_id" class="formContainer__form__field__input">
                    <?php foreach ($tasks as $task): ?>
                        <option value="<?php echo $task['id']; ?>"><?php echo $task['title']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="formContainer__form__field">
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" class="formContainer__form__field__input" required>
            </div>
            <div class="formContainer__form__field">
                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" class="formContainer__form__field__input" required>
            </div>
            <button type="submit" class="formContainer__form__button button--primary">Assign Task</button>
        </form>
    </div>
</body>

</html>