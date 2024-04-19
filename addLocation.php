<?php
include_once (__DIR__ . '/classes/Location.php');

if (!empty($_POST)) {
	try {
		$location = new Location();
		$location->setName($_POST['name']);
		$location->setAddress($_POST['address']);
		$location->setContactInfo($_POST['contact_info']);
		$location->setManagerId($_POST['manager_id']);
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
	<link rel="stylesheet" href="css/location.css">
</head>

<body>
	<div class="locationForm">
		<h2 class="locationForm__title">Add Location</h2>
		<form action="" method="post" class="locationForm__form">
			<div class="locationForm__form__field">
				<label for="location_name" class="locationForm__form__field__label">Location name:</label>
				<input type="text" id="location_name" name="name" class="locationForm__form__field__input" required>
			</div>
			<div class="locationForm__form__field">
				<label for="location_address" class="locationForm__form__field__label">Address:</label>
				<textarea id="location_address" name="address" class="locationForm__form__field__textarea"
					required></textarea>
			</div>
			<div class="locationForm__form__field">
				<label for="contact_info" class="locationForm__form__field__label">Contact info:</label>
				<input type="text" id="contact_info" name="contact_info" class="locationForm__form__field__input"
					required>
			</div>
			<div class="locationForm__form__field">
				<label for="manager_id" class="locationForm__form__field__label">Manager ID:</label>
				<input type="text" id="manager_id" name="manager_id" class="locationForm__form__field__input" required>
			</div>
			<button type="submit" class="locationForm__form__button">Add location</button>
		</form>
	</div>
</body>

</html>