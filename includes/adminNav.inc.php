<nav class="sidenav">
    <a href="index.php" class="logo">Little Sun</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="managers.php">Managers</a>
    <a href="hublocations.php">Hub Locations</a>

    <a href="logout.php" class="navbar__logout">Logout</a>
</nav>

<style>
    .sidenav {
        height: 100%;
        width: 200px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #333;
        padding-top: 20px;
    }

    .sidenav a {
        padding: 16px;
        text-decoration: none;
        font-size: 18px;
        color: #fff;
        display: block;
    }

    .sidenav a:hover {
        background-color: #555;
    }

    .navbar__logout {
        color: #fff;
        display: block;
        box-sizing: border-box;
        position: absolute;
        bottom: 20px;
        width: 100%;
        border-top: 1px solid #555;
    }
</style>