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
            <link rel="stylesheet" href="css/pagestyles/profile.css">
        </head>

        <?php include_once ("./includes/adminNav.inc.php"); ?>

        <body>
            <div class="container">
                <div class="manager-profile">
                    <div class="profile-header">
                        <img src="<?php echo $manager['profile_img']; ?>" alt="Profile Image" class="profile-img">
                        <div class="profile-info">
                            <h2 class="profile-name"><?php echo $manager['first_name'] . ' ' . $manager['last_name']; ?></h2>
                            <p class="profile-email">Email: <?php echo $manager['email']; ?></p>
                            <?php if (isset($manager['location_name'])): ?>
                                <p class="profile-location">Hub Location: <?php echo $manager['location_name']; ?></p>
                            <?php else: ?>
                                <p class="profile-location">Hub Location: Not specified</p>
                            <?php endif; ?>
                            <a href="editProfile.php?id=<?php echo $managerId; ?>" class="edit-profile-button">Edit Profile</a>
                        </div>
                    </div>
                </div>
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