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
	<link rel="stylesheet" href="css/pagestyles/hubLocations.css">
</head>

<?php include_once ("./includes/adminNav.inc.php"); ?>

<body>
	<div class="hublocations">
		<div class="hublocations__header">
			<h3 class="locationForm__title">Hub Locations</h3>
			<button onclick="window.location.href='addLocation.php'" class="button--primary">Add hub location</button>
		</div>

		<div class="hublocations__list">
			<?php foreach ($locations as $location): ?>
				<div class="hublocations__list__item">
					<div class="hublocations__list__item__top">
						<h5 class="hublocations__list__item__title">
							<?php echo $location['name']; ?>
						</h5>
						<div class="hublocations__list__item__buttons">
							<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="delete-form">
								<input type="hidden" name="location_id" value="<?php echo $location['id']; ?>">
								<button type="submit" name="delete_location" class="hublocations__list__item__delete"><i
										class="fa fa-trash"></i></button>
							</form>
							<form method="get" action="editLocation.php" class="edit-form">
								<input type="hidden" name="location_id" value="<?php echo $location['id']; ?>">
								<button type="submit" class="hublocations__list__item__edit"><i
										class="fa fa-pen"></i></button>
							</form>
						</div>
					</div>

					<div class="hublocations__list__item__details">
						<div>
							<i class="fas fa-map-marker-alt"></i>
							<span><?php echo $location['address']; ?></span>
						</div>
						<span class="hublocations__list__item__sep">|</span>
						<div>
							<i class="fas fa-phone"></i>
							<span><?php echo $location['contact_info']; ?></span>
						</div>
						<span class="hublocations__list__item__sep">|</span>
						<div>
							<i class="fas fa-user"></i>
							<span>
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