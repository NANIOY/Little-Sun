<?php
include_once (__DIR__ . '/classes/Location.php');

$locations = Location::getAll();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_location'])) {
	$locationId = $_POST['location_id'];
	try {
		Location::delete($locationId);
		header("Location: " . $_SERVER['PHP_SELF']);
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
	<title>Little Sun | Hub Locations</title>
	<link rel="stylesheet" href="css/global.css">
	<link rel="stylesheet" href="css/hubLocations.css">
</head>

<?php include_once("./includes/adminNav.inc.php"); ?>

<body>
	<div class="hublocations">
		<div class="hublocations__header">
			<h2 class="locationForm__title">Hub Locations</h2>
			<a href="addLocation.php" class="hublocations__header__button">Add hub location</a>
		</div>

		<div class="hublocations__list">
			<?php foreach ($locations as $location): ?>
				<div class="hublocations__list__item">
					<div class="hublocations__list__item__top">
						<h3 class="hublocations__list__item__title">
							<?php echo $location['name']; ?>
						</h3>
						<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="delete-form">
							<input type="hidden" name="location_id" value="<?php echo $location['id']; ?>">
							<button type="submit" name="delete_location" class="hublocations__list__item__delete">X</button>
						</form>
					</div>

					<div class="hublocations__list__item__details">
						<div class="hublocations__list__item__detail">
							<i class="fas fa-map-marker-alt"></i>
							<span class="hublocations__list__item__value"><?php echo $location['address']; ?></span>
						</div>
						<span class="hublocations__list__item__sep">|</span>
						<div class="hublocations__list__item__detail">
							<i class="fas fa-phone"></i>
							<span class="hublocations__list__item__value"><?php echo $location['contact_info']; ?></span>
						</div>
						<span class="hublocations__list__item__sep">|</span>
						<div class="hublocations__list__item__detail">
							<i class="fas fa-user"></i>
							<span class="hublocations__list__item__value">
								<?php
								$managers = Location::getManagersByLocationId($location['id']);
								foreach ($managers as $manager) {
									echo $manager['first_name'] . ' ' . $manager['last_name'] . ', ';
								}
								?>
							</span>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</body>

</html>