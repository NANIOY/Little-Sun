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

$workers = User::getAllWorkers($locationId);

$workerId = null;
if (!empty($workers)) {
    $workerId = $workers[0]['id'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $workerId = $_POST['user_id'];
    $taskId = $_POST['task_id'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $date = $_GET['date'];

    $task = Task::getTaskById($taskId);
    if (!$task) {
        echo 'Invalid task selected.';
        exit();
    }

    $response = ScheduleManager::assignSchedule($workerId, $taskId, $startTime, $endTime, $date, $locationId);

    if ($response['success']) {
        header("Location: managerDashboard.php?success=1");
        exit();
    } else {
        $errorMsg = $response['message'];
    }
}

$workerTasks = [];
if ($workerId) {
    $workerTasks = Task::getTasksByWorkerId($workerId);
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
        <form action="managerAssign.php?date=<?php echo $date; ?>" method="post" class="formContainer__form"
            id="assignForm">
            <div class="formContainer__form__field">
                <label for="user_id">Worker:</label>
                <select id="user_id" name="user_id" class="formContainer__form__field__input"
                    onchange="updateTaskList()">
                    <?php foreach ($workers as $worker): ?>
                        <option value="<?php echo $worker['id']; ?>" <?php if ($worker['id'] == $workerId)
                               echo "selected"; ?>>
                            <?php echo $worker['first_name'] . ' ' . $worker['last_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="formContainer__form__field" id="taskContainer">
                <label for="task_id">Task:</label>
                <select id="task_id" name="task_id" class="formContainer__form__field__input">
                    <?php foreach ($workerTasks as $task): ?>
                        <option value="<?php echo $task['id']; ?>"><?php echo $task['title']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
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
            <button type="submit" class="formContainer__form__button button--primary">Assign Task</button>
        </form>
    </div>
    <script>
        function updateTaskList() {
            var workerId = document.getElementById('user_id').value;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'ajax/getTasks.php?worker_id=' + workerId, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var tasks = JSON.parse(xhr.responseText);
                    var taskDropdown = document.getElementById('task_id');
                    taskDropdown.innerHTML = '';
                    tasks.forEach(function (task) {
                        var option = document.createElement('option');
                        option.value = task.id;
                        option.textContent = task.title;
                        taskDropdown.appendChild(option);
                    });
                }
            };
            xhr.send();
        }
    </script>

</body>

</html>