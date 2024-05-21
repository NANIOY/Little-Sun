<?php
session_start();
include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireManager();

if (!isset($_SESSION['user']['location_id'])) {
    echo 'Manager hub location not set.';
    exit();
}


if (!empty($_POST)) {
    try {
        $worker = new User();
        $worker->setFirstName($_POST['first_name']);
        $worker->setLastName($_POST['last_name']);
        $worker->setEmail($_POST['email']);
        $worker->setPassword($_POST['password']);

        $profileImgPath = 'uploads/' . basename($_FILES['profile_img']['name']);
        move_uploaded_file($_FILES['profile_img']['tmp_name'], $profileImgPath);
        $worker->setProfileImg($profileImgPath);

        $worker->setRole('worker');
        $worker->setHubLocation($_SESSION['user']['location_id']);
        $worker->save();

        header('Location: workers.php');
        exit();
    } catch (Throwable $th) {
        $error = $th->getMessage();
        echo "Error: " . $error;
    }
}

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Add Worker</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
    <style>

    </style>
</head>

<?php include_once ("./includes/managerNav.inc.php"); ?>

<body>
    <div class="formContainer">
        <h4 class="formContainer__title">Add Worker</h4>
        <form action="" method="post" enctype="multipart/form-data" class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="first_name" class="text-reg-s">First Name:</label>
                <input type="text" id="first_name" name="first_name"
                    class="formContainer__form__field__input text-reg-normal" required>
            </div>
            <div class="formContainer__form__field">
                <label for="last_name" class="text-reg-s">Last Name:</label>
                <input type="text" id="last_name" name="last_name"
                    class="formContainer__form__field__input text-reg-normal" required>
            </div>
            <div class="formContainer__form__field">
                <label for="email" class="text-reg-s">Email:</label>
                <input type="email" id="email" name="email" class="formContainer__form__field__input text-reg-normal"
                    required>
            </div>
            <div class="formContainer__form__field">
                <label for="password" class="text-reg-s">Password:</label>
                <input type="password" id="password" name="password"
                    class="formContainer__form__field__input text-reg-normal" required>
            </div>
            <div class="formContainer__form__field">
                <label for="profile_img" class="text-reg-s">Profile Image:</label>
                <input type="file" id="profile_img" name="profile_img"
                    class="formContainer__form__field__input text-reg-normal">
            </div>
            <button type="submit" class="formContainer__form__button button--primary">Add Worker</button>
        </form>
    </div>
</body>

</html>