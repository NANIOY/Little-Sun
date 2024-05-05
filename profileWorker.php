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
                $message = "Tasks successfully updated.";
            } catch (Exception $e) {
                $message = "Error updating tasks: " . $e->getMessage();
            }
        }

        $allTasks = Task::getAll();
        $workerTasks = Task::getTasksByWorkerId($workerId);
        $workerTaskIds = $workerTasks ? array_column($workerTasks, 'task_id') : [];

        ?><!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Worker Profile</title>
            <link rel="stylesheet" href="css/global.css">
            <link rel="stylesheet" href="css/pagestyles/workerprofile.css">
        </head>

        <?php include_once ("./includes/managerNav.inc.php"); ?>

        <body>
            <div class="workerprofile">
                <div class="workerprofile__details">
                    <img src="<?php echo htmlspecialchars($workerData['profile_img']); ?>" alt="Profile Image" class="profile__img profileimg">
                    <div>
                        <h3><?php echo htmlspecialchars($workerData['first_name']) . ' ' . htmlspecialchars($workerData['last_name']); ?></h3>
                        <p class="text-reg-normal">Email: <?php echo htmlspecialchars($workerData['email']); ?></p>
                        <?php if (!empty($message)): ?>
                        <p><?php echo $message; ?></p>
                        <?php endif; ?>
                        <button onclick="location.href='editWorkerProfile.php?id=<?php echo $workerId; ?>';" class="button--secondary">Edit Profile</button>
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
