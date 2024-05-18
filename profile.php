<?php
include_once (__DIR__ . '/classes/Manager.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireAdmin();

if (isset($_GET['id'])) {
    $managerId = $_GET['id'];
    $manager = Manager::getById($managerId);

    if ($manager) {
        ?><!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Little Sun | Manager Profile</title>
            <link rel="stylesheet" href="css/global.css">
            <link rel="stylesheet" href="css/pagestyles/profile.css">
        </head>

        <?php include_once ("./includes/adminNav.inc.php"); ?>

        <body>
            <div class="profile">
                <img src="<?php echo $manager['profile_img']; ?>" alt="Profile Image" class="profile__img profileimg">
                <div>
                    <h3><?php echo $manager['first_name'] . ' ' . $manager['last_name']; ?></h3>
                    <p class="text-reg-normal">Email: <?php echo $manager['email']; ?></p>
                    <?php if (isset($manager['location_name'])): ?>
                        <p class="text-reg-normal">Hub Location: <?php echo $manager['location_name']; ?></p>
                    <?php else: ?>
                        <p class="text-reg-normal">Hub Location: Not specified</p>
                    <?php endif; ?>
                    <button onclick="window.location.href = 'editProfile.php?id=<?php echo $managerId; ?>';"
                        class="button--secondary">Edit Profile</button>
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