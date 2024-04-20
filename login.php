<?php

    include_once (__DIR__ . '/classes/User.php');

    $users = User::getAll();

    if (!empty($_POST)) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = User::getByEmail($email);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user'] = $user;
                header('Location: index.php');
            } else {
                $error = true;
            }
        } else {
            $error = true;
        }
    }

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LITTLESUN☀️</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://use.typekit.net/qgt5qis.css">
</head>
<body>
    <header>
        <div id="navbar">
            <div id="logo"><img src="img\Little-Sun-Logo-@2x.png" alt="LittleSunLogo"></div>
            <ul><a href="#">Feature 1</a></ul>
            <ul><a href="#">Feature 2</a></ul>
            <ul><a href="#">Help</a></ul>
        </div>
    </header>
    <main>

        <div class="LittleSunTitleShiftplanner">
            <h1>Little <span style="color:yellow">Sun</span> Shiftplanner</h1>
            <p>Welcome to Little Sun Shiftplanner, the ultimate platform for shift planners in Zambia! At Little Sun Shiftplanner, we empower workers to take control of their schedules by defining their roles and selecting preferred work lactions. Our user-friendly interface allows workers ro plan their availibility for shifts and even schedule well-deserved vacations with ease.</p>
        </div>
        <div class="LittleSunLogin">
            <form action="" method="post">
                <h2>Welcome</h2>

                <?php if(isset($error)): ?>
				<div class="form__error">
					<p>
						Sorry, we can't log you in with that email address and password. Can you try again?
					</p>
				</div>
				<?php endif; ?>

                <div class="form__field">
                    <label for="Email">Email:</label>
                    <input type="text" name="email" id="username">
                </div>

                <div class="form__field">
                    <label for="Password">Password:</label>
                    <input type="password" name="password" id="password">
                </div>    

                <div class="form__field">
                    <button type="submit">Login</button>
                </div>
               
                <div class="form__field">
                    <a href="#">Forgot password?</a>
                </div>
            </form>
        </div>
    </main>
    
</body>
</html>