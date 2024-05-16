<?php
include_once(__DIR__ . '/includes/auth.inc.php');
include_once(__DIR__ . '/classes/Manager.php');
include_once(__DIR__ . '/classes/Calendar.php');
include_once(__DIR__ . '/classes/Task.php');
include_once(__DIR__ . '/classes/User.php');
include_once(__DIR__ . '/classes/WorkerReport.php');
include_once(__DIR__ . '/classes/Location.php');

requireManager();

$manager = new Manager();
$manager->setId($_SESSION['user']['id']);
$manager->setHubLocation($_SESSION['user']['location_id']);

$locationId = $manager->getHubLocation();

$tasks = Task::getAll();
$users = User::getAllWorkers($locationId); 
$locations = Location::getAll();

// Process form submission
$filteredUsers = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedLocations = isset($_POST['locations']) ? $_POST['locations'] : [];
    $selectedTasks = isset($_POST['tasks']) ? $_POST['tasks'] : [];
    $selectedUsers = isset($_POST['users']) ? $_POST['users'] : [];
    $overtime = isset($_POST['overtime']) ? true : false;

    $report = new WorkerReport();
    $filteredUsers = $report->getFilteredUsers($selectedLocations, $selectedTasks, $selectedUsers, $overtime);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Dashboard</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
    <link rel="stylesheet" href="css/pagestyles/managerdashboard.css">
</head>
<body>
    <?php include_once("./includes/managerNav.inc.php"); ?>

    <h4 class="formContainer__title">Manager dashboard</h4>

    <div class="formContainer">
        <form method="POST" action="managerdashboard.php">
            <div class="filter">
                <div class="filter__section">
                    <h4 class="filter__header">Location</h4>
                    <?php foreach ($locations as $location): ?>
                        <div>
                            <input type="checkbox" id="location-<?php echo $location['id']; ?>" name="locations[]" value="<?php echo $location['id']; ?>">
                            <label for="location-<?php echo $location['id']; ?>"><?php echo $location['name']; ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="filter__section">
                    <h4 class="filter__header">Tasks</h4>
                    <?php foreach ($tasks as $task): ?>
                        <div>
                            <input type="checkbox" id="task-<?php echo $task['id']; ?>" name="tasks[]" value="<?php echo $task['id']; ?>">
                            <label for="task-<?php echo $task['id']; ?>"><?php echo $task['title']; ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="filter__section">
                    <h4 class="filter__header--two">Users</h4>
                    <?php foreach ($users as $user): ?>
                        <div>
                            <input type="checkbox" id="user-<?php echo $user['id']; ?>" name="users[]" value="<?php echo $user['id']; ?>">
                            <label for="user-<?php echo $user['id']; ?>"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="filter__section">
                    <h4 class="filter__header">Overtime</h4>
                    <input type="checkbox" id="overtime" name="overtime" value="1">
                    <label for="overtime">Show only overtime</label>
                </div>

                <div class="filter__buttons">
                    <button type="submit" class="button--primary">Generate report</button>
                    <button type="reset" class="button--secondary">Remove Filters</button>
                </div>
            </div>
        </form>

        <?php if (!empty($filteredUsers)): ?>
            <h2>Filtered Users Report</h2>
            <ul>
                <?php foreach ($filteredUsers as $user): ?>
                    <li><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p>No users found for the selected filters.</p>
        <?php endif; ?>
    </div>
</body>
</html>
