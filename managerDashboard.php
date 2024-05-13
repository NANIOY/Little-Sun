<?php
    include_once (__DIR__ . '/includes/auth.inc.php');

    requireManager();

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Dashboard</title>
    
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
</head>

<body>
    <?php include_once ("./includes/managerNav.inc.php"); ?>

    <h4 class="formContainer__title">Manager dashboard</h4>

    <div class="formContainer">
            <div class="filter">
                <!-- Location filter -->
                <select id="location-filter">
                    <option value="">All Locations</option>
                    <option value="1">Location 1</option>
                    <option value="2">Location 2</option>
                </select>

                <!-- Person filter -->
                <select id="person-filter">
                    <option value="">All Persons</option>
                    <option value="1">Person 1</option>
                    <option value="2">Person 2</option>
                </select>

                <!-- Task type filter -->
                <select id="task-type-filter">
                    <option value="">All Task Types</option>
                    <option value="1">Task Type 1</option>
                    <option value="2">Task Type 2</option>
                </select>

                <!-- Overtime filter -->
                <label for="overtime-filter">Overtime</label>
                <input type="checkbox" id="overtime-filter">
                <button onclick="window.location.href='#'" class="button--primary">Generate report</button>
            </div>
    </div>

</body>

</html>