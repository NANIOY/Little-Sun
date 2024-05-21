<?php
session_start();
include_once (__DIR__ . '/classes/Manager.php');
include_once (__DIR__ . '/classes/Location.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireAdmin();

// check if manager ID is provided
if (!isset($_GET['id'])) {
    echo 'Manager ID not provided.';
    exit();
}

// get manager id from URL and get manager data
$managerId = $_GET['id'];
$managerData = Manager::getById($managerId);

if (!$managerData) {
    echo 'Manager not found.';
    exit();
}

$locations = Location::getAll();

// update manager data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $manager = new Manager();
    $manager->setId($managerId);
    $manager->setFirstName($_POST['first_name']);
    $manager->setLastName($_POST['last_name']);
    $manager->setEmail($_POST['email']);

    // only update password if not empty
    $password = $_POST['password'];
    if (!empty($password)) {
        $manager->setPassword($password);
    }

    $manager->setHubLocation($_POST['location']);

    // handle profile image upload
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
    <title>Little Sun | Edit Manager</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
</head>

<?php include_once ("./includes/adminNav.inc.php"); ?>

<body>
    <div class="formContainer">
        <h4 class="formContainer__title">Edit Manager Profile</h4>
        <form action="editProfile.php?id=<?php echo $managerId; ?>" method="post" enctype="multipart/form-data"
            class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="first_name" class="text-reg-s">First Name:</label>
                <input type="text" id="first_name" name="first_name"
                    class="formContainer__form__field__input text-reg-normal"
                    value="<?php echo $managerData['first_name']; ?>" required>
            </div>
            <div class="formContainer__form__field">
                <label for="last_name" class="text-reg-s">Last Name:</label>
                <input type="text" id="last_name" name="last_name"
                    class="formContainer__form__field__input text-reg-normal"
                    value="<?php echo $managerData['last_name']; ?>" required>
            </div>
            <div class="formContainer__form__field">
                <label for="email" class="text-reg-s">Email:</label>
                <input type="email" id="email" name="email" class="formContainer__form__field__input text-reg-normal"
                    value="<?php echo $managerData['email']; ?>" required>
            </div>
            <div class="formContainer__form__field">
                <label for="password" class="text-reg-s">New Password:</label>
                <input type="password" id="password" name="password"
                    class="formContainer__form__field__input text-reg-normal">
            </div>
            <div class="formContainer__form__field">
                <label for="profile_img" class="text-reg-s">Profile Image:</label>
                <input type="file" id="profile_img" name="profile_img"
                    class="formContainer__form__field__input text-reg-normal">
            </div>
            <div class="formContainer__form__field">
                <label for="location" class="text-reg-s">Location:</label>
                <select id="location" name="location" class="formContainer__form__field__input text-reg-normal"
                    required>
                    <?php foreach ($locations as $location): ?>
                        <option value="<?php echo $location['id']; ?>" <?php echo ($location['id'] == $managerData['location_id']) ? 'selected' : ''; ?>>
                            <?php echo $location['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="formContainer__form__button button--primary">Save Changes</button>
        </form>
    </div>
</body>

</html>