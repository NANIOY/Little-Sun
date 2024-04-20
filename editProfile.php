<?php
include_once (__DIR__ . '/classes/Manager.php');

if (!isset($_GET['id'])) {
    echo 'Manager ID not provided.';
    exit();
}

$managerId = $_GET['id'];
$managerData = Manager::getById($managerId);

if (!$managerData) {
    echo 'Manager not found.';
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $manager = new Manager();
    $manager->setId($managerId);
    $manager->setFirstName($_POST['first_name']);
    $manager->setLastName($_POST['last_name']);
    $manager->setEmail($_POST['email']);
    $manager->setPassword($_POST['password']);
    $manager->setHubLocation($managerData['location_id']);

    if ($_FILES['profile_img']['name']) {
        $profileImgPath = 'uploads/' . basename($_FILES['profile_img']['name']);
        move_uploaded_file($_FILES['profile_img']['tmp_name'], $profileImgPath);
        $manager->setProfileImg($profileImgPath);
    } else {
        $manager->setProfileImg($managerData['profile_img']);
    }

    $manager->update();
    header("Location: profile.php?id=$managerId");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Manager Profile</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/form.css">
</head>

<body>
    <div class="formContainer">
        <h2 class="formContainer__title">Edit Manager Profile</h2>
        <form action="editProfile.php?id=<?php echo $managerId; ?>" method="post" enctype="multipart/form-data"
            class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="first_name" class="formContainer__form__field__label">First Name:</label>
                <input type="text" id="first_name" name="first_name" class="formContainer__form__field__input"
                    value="<?php echo $managerData['first_name']; ?>" required>
            </div>
            <div class="formContainer__form__field">
                <label for="last_name" class="formContainer__form__field__label">Last Name:</label>
                <input type="text" id="last_name" name="last_name" class="formContainer__form__field__input"
                    value="<?php echo $managerData['last_name']; ?>" required>
            </div>
            <div class="formContainer__form__field">
                <label for="email" class="formContainer__form__field__label">Email:</label>
                <input type="email" id="email" name="email" class="formContainer__form__field__input"
                    value="<?php echo $managerData['email']; ?>" required>
            </div>
            <div class="formContainer__form__field">
                <label for="password" class="formContainer__form__field__label">New Password:</label>
                <input type="password" id="password" name="password" class="formContainer__form__field__input" required>
            </div>
            <div class="formContainer__form__field">
                <label for="profile_img" class="formContainer__form__field__label">Profile Image:</label>
                <input type="file" id="profile_img" name="profile_img" class="formContainer__form__field__input">
            </div>
            <button type="submit" class="formContainer__form__button">Save Changes</button>
        </form>
    </div>
</body>

</html>