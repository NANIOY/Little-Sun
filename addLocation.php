<?php
include_once (__DIR__ . '/classes/Location.php');

if (!empty($_POST)) {
	try {
		$location = new Location();
		$location->setName($_POST['name']);
		$location->setAddress($_POST['address']);
		$location->setContactInfo($_POST['contact_info']);
		$location->save();

		header('Location: hubLocations.php');
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
	<title>Little Sun | Add Location</title>
	<link rel="stylesheet" href="css/global.css">
	<link rel="stylesheet" href="css/pagestyles/form.css">
</head>

<?php include_once ("./includes/adminNav.inc.php"); ?>

<body>
	<div class="formContainer">
		<h4 class="formContainer__title">Add Location</h4>
		<form action="" method="post" class="formContainer__form">
			<div class="formContainer__form__field">
				<label for="location_name" class="text-reg-s">Location name:</label>
				<input type="text" id="location_name" name="name"
					class="formContainer__form__field__input text-reg-normal" required>
			</div>
			<div class="formContainer__form__field">
				<label for="location_address" class="text-reg-s">Address:</label>
				<textarea id="location_address" name="address"
					class="formContainer__form__field__textarea text-reg-normal" required></textarea>
			</div>
			<div class="formContainer__form__field">
				<label for="contact_info" class="text-reg-s">Contact info:</label>
				<input type="text" id="contact_info" name="contact_info"
					class="formContainer__form__field__input text-reg-normal" required>
			</div>
			<button type="submit" class="formContainer__form__button button--primary">Add location</button>
		</form>
	</div>
</body>

</html>