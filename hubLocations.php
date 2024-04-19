<?php

include_once (__DIR__ . '/classes/Location.php');

$locations = Location::getAll();

?><!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Hub Locations</title>
	<link rel="stylesheet" href="css/global.css">
	<link rel="stylesheet" href="css/hubLocations.css">
</head>

<body>
	<div class="hublocations">

		<div class="hublocations__header">
			<h2 class="locationForm__title">Hub Locations</h2>
			<button type="button" class="hublocations__header__button">Add hub location</button>
		</div>

		<div class="hublocations__list">
			<?php foreach ($locations as $location): ?>
				<div class="hublocations__list__item">
					<div class="hublocations__list__item__top">
						<h3 class="hublocations__list__item__title">
							<?php echo $location['name']; ?>
						</h3>
						<button type="button" class="hublocations__list__item__delete">X</button>
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
							<span class="hublocations__list__item__value"><?php echo $location['manager_id']; ?></span>
						</div>
					</div>

				</div>
			<?php endforeach; ?>
		</div>
	</div>
</body>

</html>