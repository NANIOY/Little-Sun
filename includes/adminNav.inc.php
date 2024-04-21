<nav class="sidenav">
    <a href="index.php" class="logo text-white"><img src="src\img\Little-Sun-Logo-@2x.png" alt="LittleSunLogo"></a>
    <a href="dashboard.php" class="text-white"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="managers.php" class="text-white"><i class="fas fa-users"></i> Managers</a>
    <a href="hublocations.php" class="text-white"><i class="fas fa-map-marker-alt"></i> Hub Locations</a>
    <a href="logout.php" class="navbar__logout text-white"><i class="fas fa-sign-out-alt"></i> Logout</a>
</nav>

<style>
    .sidenav {
        height: 100%;
        width: 200px;
        position: fixed;
        top: 0;
        left: 0;
        padding-top: 20px;
        background-color: var(--black);
    }

    .logo img {
        width: 100%;
    }

    .sidenav a {
        padding: 16px;
        text-decoration: none;
        font-size: 18px;
        display: flex;
        transition: 0.3s;
    }

    .sidenav a:hover {
        color: var(--blurple);
    }

    .sidenav a i {
        margin-right: 10px;
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