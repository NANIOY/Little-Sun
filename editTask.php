<?php
include_once (__DIR__ . '/classes/Task.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireAdmin();

// check if task ID is provided
if (!isset($_GET['task_id'])) {
    echo 'Task ID not provided.';
    exit();
}

// get task id from URL and get task data
$taskId = $_GET['task_id'];
$taskData = Task::getById($taskId);

if (!$taskData) {
    echo 'Task not found.';
    exit();
}

// update task data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task = new Task();
    $task->setId($taskId);
    $task->setTitle($_POST['title']);
    $task->setColor($_POST['color']);
    $task->update();
    header("Location: tasks.php");
    exit();
}

include_once ("./includes/adminNav.inc.php");
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Edit Task</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
</head>

<body>
    <div class="formContainer">
        <h4 class="formContainer__title">Edit Task</h4>
        <form action="" method="post" enctype="multipart/form-data" class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="title" class="text-reg-s">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($taskData['title']); ?>"
                    class="formContainer__form__field__input text-reg-normal" required>
            </div>

            <div class="formContainer__form__field">
                <label class="text-reg-s">Color:</label>
                <div class="color">
                    <?php
                    $colors = ['#FF6976', '#F069FF', '#7A68FE', '#69B4FF', '#69F0AE', '#69FFB7', '#E9FF69', '#FFDA69', '#900017', '#620078', '#050094', '#0061A6', '#008587', '#007A35', '#727A00', '#795000'];
                    foreach ($colors as $color) {
                        $checked = $taskData['color'] == $color ? ' checked' : '';
                        echo "<input type='radio' id='color{$color}' name='color' value='{$color}' class='color__input'{$checked}>
                              <label for='color{$color}' class='color__choice' style='background-color: {$color};'></label>";
                    }
                    ?>
                </div>
            </div>

            <button type="submit" class="formContainer__form__button button--primary">Update Task</button>
        </form>
    </div>
</body>

</html>