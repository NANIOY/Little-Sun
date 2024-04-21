<nav class="sidenav">
    <div class="sidenav__top">
        <a href="index.php" class="logo text-white"><img src="src\img\Little-Sun-Logo-@2x.png" alt="LittleSunLogo"></a>
        <a href="dashboard.php" class="text-white"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="#" class="text-white"><i class="fas fa-users"></i> Workers</a>
        <a href="#" class="text-white"><i class="fas fa-tasks"></i> Tasks</a>
        <a href="#" class="text-white"><i class="far fa-calendar"></i> Schedule</a>
    </div>

    <div class="sidenav__bottom">
        <?php
        session_start();
        if (isset($_SESSION["user"])) {
            $user = $_SESSION["user"];
            ?>
            <div class="sidenav__user">
                <img src="<?= $user['profile_img'] ?>" alt="Profile Image" class="sidenav__user__img profileimg">
                <div>
                    <span class="text-bold-normal text-white"><?= $user['first_name'] ?>     <?= $user['last_name'] ?></span>
                    <span class="badge text-bold-xs">Manager</span>
                </div>
            </div>
        <?php } ?>
        <a href="logout.php" class="navbar__logout text-white"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</nav>

<style>
    .sidenav {
        height: 100%;
        width: 240px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: var(--black);
        padding: 20px 24px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        user-select: none;
    }

    .sidenav__top {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .logo img {
        width: 100%;
        margin-bottom: 8px;
    }

    .sidenav a {
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--white);
        transition: color 0.3s;
    }

    .sidenav a:hover {
        color: var(--blurple);
    }

    .sidenav__bottom {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        gap: 24px;
        margin-bottom: 8px;
    }

    .sidenav__user {
        display: flex;
        align-items: center;
        margin-top: auto;
    }

    .sidenav__user__img {
        width: 40px;
        height: 40px;
        margin-right: 12px;
        border-radius: 50%;
    }

    .sidenav__user div {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 2px;
    }

    .badge {
        background-color: var(--green);
        padding: 2px 4px;
        border-radius: 4px;
        box-sizing: border-box;
        text-transform: uppercase;
    }
</style>