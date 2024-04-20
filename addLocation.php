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
	<title>Add Location</title>
	<link rel="stylesheet" href="css/global.css">
	<link rel="stylesheet" href="css/form.css">
</head>

<body>
	<div class="formContainer">
		<h2 class="formContainer__title">Add Location</h2>
		<form action="" method="post" class="formContainer__form">
			<div class="formContainer__form__field">
				<label for="location_name" class="formContainer__form__field__label">Location name:</label>
				<input type="text" id="location_name" name="name" class="formContainer__form__field__input" required>
			</div>
			<div class="formContainer__form__field">
				<label for="location_address" class="formContainer__form__field__label">Address:</label>
				<textarea id="location_address" name="address" class="formContainer__form__field__textarea"
					required></textarea>
			</div>
			<div class="formContainer__form__field">
				<label for="contact_info" class="formContainer__form__field__label">Contact info:</label>
				<input type="text" id="contact_info" name="contact_info" class="formContainer__form__field__input"
					required>
			</div>
			<button type="submit" class="formContainer__form__button">Add location</button>
		</form>
	</div>
</body>

</html>