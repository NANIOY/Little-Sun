<?php
include_once (__DIR__ . '/classes/Manager.php');

if (isset($_GET['id'])) {
    $managerId = $_GET['id'];
    $manager = Manager::getById($managerId);

    if ($manager) {

        ?><!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Manager Profile</title>
            <link rel="stylesheet" href="css/global.css">
        </head>

        <body>
            <div class="manager-profile">
                <h2><?php echo $manager['first_name'] . ' ' . $manager['last_name']; ?></h2>
                <p>Email: <?php echo $manager['email']; ?></p>
                <?php if (isset($manager['location_name'])): ?>
                    <p>Hub Location: <?php echo $manager['location_name']; ?></p>
                <?php else: ?>
                    <p>Hub Location: Not specified</p>
                <?php endif; ?>
                <img src="<?php echo $manager['profile_img']; ?>" alt="Profile Image">
            </div>
        </body>

        </html>
        <?php
    } else {
        echo 'Manager not found.';
    }
} else {
    echo 'Manager ID not provided.';
}
?>