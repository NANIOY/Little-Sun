<nav class="sidenav">
    <a href="index.php" class="logo text-white"><img src="src\img\Little-Sun-Logo-@2x.png" alt="LittleSunLogo"></a>
    <a href="dashboard.php" class="text-white"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="#" class="text-white"><i class="fas fa-users"></i> Workers</a>
    <a href="#" class="text-white"><i class="fas fa-tasks"></i> Tasks</a>
    <a href="#" class="text-white"><i class="far fa-calendar"></i> Schedule</a>
    <a href="logout.php" class="navbar__logout text-white"><i class="fas fa-sign-out-alt"></i> Logout</a>
</nav>

<style>
    .sidenav {
        height: 100%;
        width: 240px;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 20px;
        background-color: var(--black);
    }

    .logo img {
        width: 80%;
    }

    .sidenav a {
        padding: 16px;
        text-decoration: none;
        font-size: 18px;
        display: flex;
        gap: 12px;
        transition: 0.3s;
    }

    .sidenav a:hover {
        color: var(--blurple);
    }


    .navbar__logout {
        display: block;
        box-sizing: border-box;
        position: absolute;
        bottom: 20px;
        width: 100%;
        border-top: 1px solid var(--gray);
    }
</style>