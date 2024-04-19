<?php

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Location</title>
    <link rel="stylesheet" href="css/location.css">
</head>

<body>
    <div class="locationForm">
        <h2 class="locationForm__title">Add Location</h2>
        <form action="#" method="post" class="locationForm__form">
            <div class="locationForm__form__field">
                <label for="location-name" class="locationForm__form__field__label">Location name:</label>
                <input type="text" id="location-name" name="location-name" class="locationForm__form__field__input"
                    required>
            </div>
            <div class="locationForm__form__field">
                <label for="location-address" class="locationForm__form__field__label">Address:</label>
                <textarea id="location-address" name="location-address" class="locationForm__form__field__textarea"
                    required></textarea>
            </div>
            <div class="locationForm__form__field">
                <label for="contact-info" class="locationForm__form__field__label">Contact info:</label>
                <input type="text" id="contact-info" name="contact-info" class="locationForm__form__field__input"
                    required>
            </div>
            <div class="locationForm__form__field">
                <label for="manager-id" class="locationForm__form__field__label">Manager ID:</label>
                <input type="text" id="manager-id" name="manager-id" class="locationForm__form__field__input" required>
            </div>
            <button type="submit" class="locationForm__form__button">Add location</button>
        </form>
    </div>
</body>

</html>