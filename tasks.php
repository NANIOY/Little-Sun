<?php
session_start();
include_once (__DIR__ . '/classes/Task.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireAdmin();

$tasks = Task::getAll();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_task'])) {
    $taskId = $_POST['task_id'];
    try {
        Task::delete($taskId);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Throwable $th) {
        $error = $th->getMessage();
    }
}

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Tasks</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/managers.css">
    <link rel="stylesheet" href="css/pagestyles/tasks.css">
    <link rel="stylesheet" href="css/pagestyles/hublocations.css">
</head>

<?php include_once ("./includes/adminNav.inc.php"); ?>

<body>
    <div class="managers">
        <div class="managers__header">
            <h3>All Tasks</h3>
            <button onclick="window.location.href='addTask.php'" class="button--primary">Add Task</button>
        </div>

        <div class="tasklist">
            <?php foreach ($tasks as $task): ?>
                <div class="tasklist__tag text-bold-normal"
                    style="background-color: <?php echo htmlspecialchars($task['color']); ?>">
                    <?php echo $task['title']; ?>
                    <div class="tasklist__tag__buttons">
                        <form method="get" action="editTask.php" class="edit-form">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <button type="submit" class="hublocations__list__item__edit"><i class="fa fa-pen"></i></button>
                        </form>
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="delete-form">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <button type="submit" name="delete_task" class="hublocations__list__item__delete"><i
                                    class="fa fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>