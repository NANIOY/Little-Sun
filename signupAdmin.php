<?php
        if( !empty($_POST) ) {
            $email = $_POST["email"];
          
            $options =[
                'cost' => 14,
            ];

            $password = password_hash($_POST['password'], PASSWORD_DEFAULT, $options); // password_hash is veilig DEFAULT is BCRYPT

            $conn = new mysqli("127.0.0.1", "root", "", "littlesun");
            $query = "insert into users (first_name, last_name, email, password, role) values ('$firstname', '$lastname','$email', '$password')";
            $result = $conn->query($query);
            header("Location: index.php");
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
                <h2>Sign up Admin</h2>

                <div class="form__field">
                    <label for="Firstname">FirstName:</label>
                    <input type="text" name="firstname" id="firstname">
                </div>

                <div class="form__field">
                    <label for="Lastname">Lastname:</label>
                    <input type="text" name="lastname" id="lastname">
                </div>

                <div class="form__field">
                    <label for="Email">Username:</label>
                    <input type="text" name="email" id="username">
                </div>

                <div class="form__field">
                    <label for="Password">Password:</label>
                    <input type="password" name="password" id="password">
                </div>

                <div class="form__field">
                    <label for="Role">Role:</label>
                    <input type="text" name="role" id="role">
                </div>  

                <div class="form__field">
                    <button type="submit">Sign up</button>
                </div>
               
                <div class="form__field">
                    <a href="login.php">Already have an account?</a>
                </div>
            </form>
        </div>
    </main>
    
</body>
</html>