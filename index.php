<?php
$file = __DIR__ . '/classes/User.php';
if (file_exists($file)) {
    echo "File exists: $file<br>";
    include_once ($file);
    echo "User.php included successfully<br>";
} else {
    echo "File does not exist: $file<br>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LITTLESUN ☀️ | Login </title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pagestyles/form.css">
    <link rel="stylesheet" href="css/pagestyles/login.css">
</head>

<body>
    <main>

        <div class="LittleSunTitleShiftplanner">
            <h2>Little <strong style="color:#F7BD01;">Sun</strong> Shiftplanner</h2>
            <p>Welcome to Little Sun Shiftplanner, the ultimate platform for shift planners in Zambia! At Little Sun
                Shiftplanner, we empower workers to take control of their schedules by defining their roles and
                selecting preferred work locations. Our user-friendly interface allows workers to plan their
                availability
                for shifts and even schedule well-deserved vacations with ease.</p>
        </div> 

        <div class="formContainer">
        <h4 class="formContainer__title">Welcome</h4>
        <form action="" method="post" enctype="multipart/form-data" class="formContainer__form">

            <?php if (isset($error)): ?>
                <div class="form__error">
                    <p>
                        Sorry, we can't log you in with that email address and password. Can you try again?
                    </p>
                </div>
            <?php endif; ?>

            <div class="formContainer__form__field">
                <label for="email" class="text-reg-s">Email:</label>
                <input type="email" id="email" name="email" class="formContainer__form__field__input text-reg-normal" required>
            </div>
            
            <div class="formContainer__form__field">
                <label for="password" class="text-reg-s">Password:</label>
                <input type="password" id="password" name="password"
                    class="formContainer__form__field__input text-reg-normal" required>
            </div>

            <div class="formContainer__form__field">
                <a href="#">Forgot password?</a>
            </div>
           
            <button type="submit" class="formContainer__form__button button--primary">Log In</button>
        </form>
    </div>


    </main>

</body>

</html>
