<?php
include_once (__DIR__ . '/classes/User.php');
include_once (__DIR__ . '/classes/Location.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireWorker();

if (!isset($_SESSION['user']['location_id'])) {
    echo 'Worker hub location not set.';
    exit();
}

$locations = Location::getAll();

$locationId = $_SESSION['user']['location_id'];
$workers = User::getAllWorkers($locationId);


$workerId = $_GET['id'];
$workerData = User::getById($workerId);

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

        header('Location: workerSchedule.php');
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
    <title>Little Sun | Request Time Off</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
    <style>

    </style>
</head>

<?php include_once ("./includes/workerNav.inc.php"); ?>

<body>
    <div class="formContainer">
        <h4 class="formContainer__title">Request time off</h4>

        <form action="" method="post" enctype="multipart/form-data" class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="first_name" class="text-reg-s">Name</label>
                <?php echo $worker['first_name'] . ' ' . $worker['last_name']; ?>
            </div>

            <div class="formContainer__form__field">
                <label for="first_name" class="text-reg-s">Location</label>
                <?php echo $location['name']; ?>
            </div>


            <div class="formContainer__form__field">
                <label for="location_id" class="text-reg-s">Hub Location:</label>
                <select id="location_id" name="location_id" class="formContainer__form__field__input text-reg-normal"
                    required>
                    <?php foreach ($locations as $location): ?>
                        <option value="<?= $location['id'] ?>"><?= htmlspecialchars($location['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <button type="submit" class="formContainer__form__button button--primary">submit</button>
        </form>
    </div>
</body>

</html>