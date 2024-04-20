<?php
    include_once (__DIR__ . '/classes/User.php');
    
    session_start();
    if(isset($_SESSION['user'])){
        echo 'Welcome ' . $_SESSION['user']['first_name'];
    }else{
        header('Location: login.php');
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
            <a href="#">Feature 1</a>
            <a href="#">Feature 2</a>
            <a href="#" class="LoggedIn">
                <div class="user-avatar"><img src="https://cdn.pixabay.com/photo/2016/11/18/23/38/child-1837375_640.png" width="50px" alt="user avatar"></div>
                
                <?php if(isset($_SESSION['user']['first_name'])): ?>
                <h3><?php echo $_SESSION['user']['first_name']; ?></h3>
                <?php else: ?> 
                <h3>User name here</h3>
                <?php endif; ?>
            </a>

            <a href="logout.php">Log out?</a>
        </div>
    </header>
    <main>

       
    </main>
    
</body>
</html>