<?php
include_once (__DIR__ . '/classes/Manager.php');

$managers = Manager::getAll();

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Managers</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/managers.css">
</head>

<?php include_once ("./includes/adminNav.inc.php"); ?>

<body>
    <div class="managers">
        <div class="managers__header">
            <h2>All Managers</h2>
            <a href="addManager.php" class="managers__header__button">Add manager</a>
        </div>

        <div class="managercards">
            <?php foreach ($managers as $manager): ?>
                <a href="profile.php?id=<?php echo $manager['id']; ?>" class="managercard">
                    <img src="<?php echo $manager['profile_img']; ?>" alt="Profile Image" class="managercard__img">
                    <div class="managercard__info">
                        <div class="managercard__name">
                            <?php echo $manager['first_name'] . ' ' . $manager['last_name']; ?>
                        </div>
                        <?php if (isset($manager['location_name'])): ?>
                            <div class="managercard__hub">
                                Hub Location: <?php echo $manager['location_name']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>