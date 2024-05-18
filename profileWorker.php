<?php
include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/classes/Task.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireManager();

if (isset($_GET['id'])) {
    $workerId = $_GET['id'];
    $workerData = User::getById($workerId);

    if ($workerData) {
        $worker = new User();
        $worker->setId($workerData['id']);
        $worker->setFirstName($workerData['first_name']);
        $worker->setLastName($workerData['last_name']);
        $worker->setEmail($workerData['email']);
        $worker->setProfileImg($workerData['profile_img']);
        $worker->setHubLocation($workerData['location_id']);
        $worker->setRole($workerData['role']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_ids'])) {
            try {
                $worker->assignTasks($_POST['task_ids']);
            } catch (Exception $e) {
                $message = "Error updating tasks: " . $e->getMessage();
            }
        }

        $allTasks = Task::getAll();
        $workerTasks = Task::getTasksByWorkerId($workerId);
        $workerTaskIds = $workerTasks ? array_column($workerTasks, 'id') : [];
        ?><!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Little Sun | Worker Profile</title>
            <link rel="stylesheet" href="css/global.css">
            <link rel="stylesheet" href="css/pagestyles/tasks.css">
            <link rel="stylesheet" href="css/pagestyles/workerprofile.css">
        </head>

        <?php include_once ("./includes/managerNav.inc.php"); ?>

        <body>
            <div class="workerprofile">
                <div class="workerprofile__details">
                    <img src="<?php echo htmlspecialchars($workerData['profile_img']); ?>" alt="Profile Image"
                        class="profile__img profileimg">
                    <div>
                        <h3><?php echo htmlspecialchars($workerData['first_name']) . ' ' . htmlspecialchars($workerData['last_name']); ?>
                        </h3>
                        <p class="text-reg-normal">Email: <?php echo htmlspecialchars($workerData['email']); ?></p>
                        <div class="tasklist">
                            <?php foreach ($workerTasks as $task): ?>
                                <div class="tasklist__tag text-bold-normal"
                                    style="background-color: <?php echo htmlspecialchars($task['color']); ?>">
                                    <?php echo htmlspecialchars($task['title']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button onclick="location.href='editWorkerProfile.php?id=<?php echo $workerId; ?>';"
                            class="button--secondary">Edit Profile</button>
                    </div>
                </div>

                <div class="profile__tasks">
                    <h4>Assign Tasks</h4>
                    <form action="" method="post">
                    <ul>
                        <?php foreach ($allTasks as $task): ?>
                            <li>
                                <label class="profile__tasks__label">
                                    <input type="checkbox" name="task_ids[]" value="<?php echo $task['id']; ?>"
                                        <?php echo in_array($task['id'], $workerTaskIds) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($task['title']); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="submit" class="button--primary">Assign Tasks</button>
                </form>
                </div>
            </div>
        </body>

        </html>
        <?php
    } else {
        echo 'Worker not found.';
    }
} else {
    echo 'Worker ID not provided.';
}
?>