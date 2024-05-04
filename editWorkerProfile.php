<?php
include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/classes/Location.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireManager();

// check if worker ID is provided
if (!isset($_GET['id'])) {
    echo 'Worker ID not provided.';
    exit();
}

// get worker id from URL and get worker data
$workerId = $_GET['id'];
$workerData = User::getById($workerId);

if (!$workerData) {
    echo 'Worker not found.';
    exit();
}


// update worker data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $worker = new User();
    $worker->setId($workerId);
    $worker->setFirstName($_POST['first_name']);
    $worker->setLastName($_POST['last_name']);
    $worker->setEmail($_POST['email']);

    // only update password if not empty
    $password = $_POST['password'];
    if (!empty($password)) {
        $worker->setPassword($password);
    }


    // handle profile image upload
    if ($_FILES['profile_img']['name']) {
        $profileImgPath = 'uploads/' . basename($_FILES['profile_img']['name']);
        move_uploaded_file($_FILES['profile_img']['tmp_name'], $profileImgPath);
        $worker->setProfileImg($profileImgPath);
    } else {
        $worker->setProfileImg($workerData['profile_img']);
    }

    $worker->update();
    header("Location: profileWorker.php?id=$workerId");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Worker Profile</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
</head>

<?php include_once ("./includes/managerNav.inc.php"); ?>

<body>
    <div class="formContainer">
        <h4 class="formContainer__title">Edit Worker Profile</h4>
        <form action="editProfile.php?id=<?php echo $workerId; ?>" method="post" enctype="multipart/form-data"
            class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="first_name" class="text-reg-s">First Name:</label>
                <input type="text" id="first_name" name="first_name"
                    class="formContainer__form__field__input text-reg-normal"
                    value="<?php echo $workerData['first_name']; ?>" required>
            </div>
            <div class="formContainer__form__field">
                <label for="last_name" class="text-reg-s">Last Name:</label>
                <input type="text" id="last_name" name="last_name"
                    class="formContainer__form__field__input text-reg-normal"
                    value="<?php echo $workerData['last_name']; ?>" required>
            </div>
            <div class="formContainer__form__field">
                <label for="email" class="text-reg-s">Email:</label>
                <input type="email" id="email" name="email" class="formContainer__form__field__input text-reg-normal"
                    value="<?php echo $workerData['email']; ?>" required>
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
            <button type="submit" class="formContainer__form__button button--primary">Save Changes</button>
        </form>
    </div>
</body>

</html>