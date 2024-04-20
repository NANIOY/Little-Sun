<?php
include_once (__DIR__ . '/classes/Manager.php');
include_once (__DIR__ . '/classes/Location.php');

$locations = Location::getAll();

if (!empty($_POST)) {
    try {
        $manager = new Manager();
        $manager->setFirstName($_POST['first_name']);
        $manager->setLastName($_POST['last_name']);
        $manager->setEmail($_POST['email']);
        $manager->setPassword($_POST['password']);

        $profileImgPath = 'uploads/' . basename($_FILES['profile_img']['name']);
        move_uploaded_file($_FILES['profile_img']['tmp_name'], $profileImgPath);

        $manager->setProfileImg($profileImgPath);

        $manager->setHubLocation($_POST['location_id']);
        $manager->save();

        $locationId = $_POST['location_id'];
        $manager->assignToLocation($locationId);

        header('Location: managers.php');
        exit();
    } catch (Throwable $th) {
        $error = $th->getMessage();
    }
}
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Add Manager</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/form.css">
</head>

<body>
    <div class="formContainer">
        <h2 class="formContainer__title">Add Manager</h2>
        <form action="" method="post" enctype="multipart/form-data" class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="first_name" class="formContainer__form__field__label">First Name:</label>
                <input type="text" id="first_name" name="first_name" class="formContainer__form__field__input" required>
            </div>
            <div class="formContainer__form__field">
                <label for="last_name" class="formContainer__form__field__label">Last Name:</label>
                <input type="text" id="last_name" name="last_name" class="formContainer__form__field__input" required>
            </div>
            <div class="formContainer__form__field">
                <label for="email" class="formContainer__form__field__label">Email:</label>
                <input type="email" id="email" name="email" class="formContainer__form__field__input" required>
            </div>
            <div class="formContainer__form__field">
                <label for="password" class="formContainer__form__field__label">Password:</label>
                <input type="password" id="password" name="password" class="formContainer__form__field__input" required>
            </div>
            <div class="formContainer__form__field">
                <label for="profile_img" class="formContainer__form__field__label">Profile Image:</label>
                <input type="file" id="profile_img" name="profile_img" class="formContainer__form__field__input">
            </div>
            <div class="formContainer__form__field">
                <label for="location_id" class="formContainer__form__field__label">Hub Location:</label>
                <select id="location_id" name="location_id" class="formContainer__form__field__input" required>
                    <?php foreach ($locations as $location): ?>
                        <option value="<?= $location['id'] ?>"><?= htmlspecialchars($location['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="formContainer__form__button">Add Manager</button>
        </form>
    </div>
</body>

</html>