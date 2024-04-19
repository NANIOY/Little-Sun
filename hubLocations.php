<?php
include_once(__DIR__ . '/classes/Location.php');

$locations = Location::getAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hub Locations</title>
</head>

<body>
    <div class="location-list">
        <h2>Hub Locations</h2>
        <ul>
            <?php foreach ($locations as $location): ?>
            <li>
                <strong>Name:</strong>
                <?php echo $location['name']; ?><br>
                <strong>Address:</strong>
                <?php echo $location['address']; ?><br>
                <strong>Contact Info:</strong>
                <?php echo $location['contact_info']; ?><br>
                <strong>Manager ID:</strong>
                <?php echo $location['manager_id']; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>

</html>