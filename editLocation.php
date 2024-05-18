<?php
include_once (__DIR__ . '/classes/Location.php');
include_once (__DIR__ . '/includes/auth.inc.php');

requireAdmin();

// check if location ID is provided
if (!isset($_GET['location_id'])) {
    echo 'Location ID not provided.';
    exit();
}

// get location id from URL and get location data
$locationId = $_GET['location_id'];
$locationData = Location::getById($locationId);

if (!$locationData) {
    echo 'Location not found.';
    exit();
}

// update location data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $location = new Location();
    $location->setId($locationId);
    $location->setName($_POST['name']);
    $location->setAddress($_POST['address']);
    $location->setContactInfo($_POST['contact_info']);
    $location->update();
    header("Location: hubLocations.php");
    exit();
}

$locations = Location::getAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Little Sun | Edit Location</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
</head>

<?php include_once ("./includes/adminNav.inc.php"); ?>

<body>
    <div class="formContainer">
        <h4 class="formContainer__title">Edit Location</h4>
        <form action="editLocation.php?location_id=<?php echo $locationId; ?>" method="post"
            class="formContainer__form">
            <div class="formContainer__form__field">
                <label for="name" class="text-reg-s">Name:</label>
                <input type="text" id="name" name="name" class="formContainer__form__field__input text-reg-normal"
                    value="<?php echo $locationData['name']; ?>" required>
            </div>
            <div class="formContainer__form__field">
                <label for="address" class="text-reg-s">Address:</label>
                <input type="text" id="address" name="address" class="formContainer__form__field__input text-reg-normal"
                    value="<?php echo $locationData['address']; ?>" required>
            </div>
            <div class="formContainer__form__field">
                <label for="contact_info" class="text-reg-s">Contact Info:</label>
                <input type="text" id="contact_info" name="contact_info"
                    class="formContainer__form__field__input text-reg-normal"
                    value="<?php echo $locationData['contact_info']; ?>" required>
            </div>
            <button type="submit" class="formContainer__form__button button--primary">Save Changes</button>
        </form>
    </div>
</body>

</html>