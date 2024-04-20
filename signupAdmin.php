<?php

    include_once (__DIR__ . '/classes/Admin.php');

    if (!empty($_POST)) {
        try {
            $admin = new Admin();
            $admin->setFirstName($_POST['first_name']);
            $admin->setLastName($_POST['last_name']);
            $admin->setEmail($_POST['email']);
            $admin->setPassword($_POST['password']);
            $admin->save();

            var_dump($admin);

            header('Location: login.php');
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
        <div class="LittleSunLogin">
            <form action="" method="post">
                <h2>add Admin</h2>

                <div class="form__field">
                    <label for="Firstname">Firstname:</label>
                    <input type="text" name="first_name" id="first_name">
                </div>

                <div class="form__field">
                    <label for="Lastname">Lastname:</label>
                    <input type="text" name="last_name" id="last_name">
                </div>

                <div class="form__field">
                    <label for="Email">Email:</label>
                    <input type="text" name="email" id="email" required>
                </div>

                <div class="form__field">
                    <label for="Password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="form__field">
                    <button type="submit">add Admin</button>
                </div>
            </form>
        </div>
    </main>
    
</body>
</html>