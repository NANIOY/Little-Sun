<?php
    include_once (__DIR__ . '/includes/auth.inc.php');
    include_once (__DIR__ . '/classes/Manager.php');
    include_once (__DIR__ . '/classes/Calendar.php');
    include_once (__DIR__ . '/classes/Task.php');
    include_once (__DIR__ . '/classes/User.php');

    requireManager();

    $manager = new Manager();
    $manager->setId($_SESSION['user']['id']);
    $manager->setHubLocation($_SESSION['user']['location_id']);

    $locationId = $manager->getHubLocation();

    $tasks = Task::getAll();
    $workers = User::getAllWorkers($locationId);

?><!DOCTYPE html>
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
    <?php include_once ("./includes/managerNav.inc.php"); ?>

    <h4 class="formContainer__title">Manager dashboard</h4>

    <div class="formContainer">
    <div class="filter">
        <div class="filter__section">
            <h4 class="filter__header">Location</h4>
            <?php foreach ($tasks as $task): ?>
                <div>
                    <input type="checkbox" id="task-<?php echo $task['id']; ?>" name="tasks"
                        value="<?php echo $task['id']; ?>">
                    <label for="task-<?php echo $task['id']; ?>"><?php echo $task['title']; ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="filter__section">
            <h4 class="filter__header">Tasks</h4>
            <?php foreach ($tasks as $task): ?>
                <div>
                    <input type="checkbox" id="task-<?php echo $task['id']; ?>" name="tasks"
                        value="<?php echo $task['id']; ?>">
                    <label for="task-<?php echo $task['id']; ?>"><?php echo $task['title']; ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="filter__section">      
            <h4 class="filter__header--two">Workers</h4>
            <?php foreach ($workers as $worker): ?>
                <div>
                    <input type="checkbox" id="worker-<?php echo $worker['id']; ?>" name="workers"
                        value="<?php echo $worker['id']; ?>">
                    <label
                        for="worker-<?php echo $worker['id']; ?>"><?php echo $worker['first_name'] . ' ' . $worker['last_name']; ?></label>
                </div>
            <?php endforeach; ?>
        </div>    

        <div class="filter__section">
            <h4 class="filter__header">Overtime</h4>
            <!-- OVERTIME NEEDS TO BE IMPLEMENTED AS FILTER -->
            <input type="checkbox" id="overtime" name="overtime" value="1">
            <label for="overtime">Show only overtime</label>
        </div>
                
        <div class="filter__buttons">
                <button class="button--secondary" onclick="applyFilters()">Apply Filters</button>
                <button class="button--tertiary" onclick="removeFilters()">Remove Filters</button>
        </div>
        
        </div>
        <button onclick="window.location.href='#'" class="button--primary">Generate report</button>
    </div>

</body>

</html>