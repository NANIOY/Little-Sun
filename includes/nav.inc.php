
<nav class="sidenav">
    <div class="sidenav__top">
        <a href="index.php" class="logo text-white"><img src="src\img\Little-Sun-Logo-@2x.png" alt="LittleSunLogo"></a>
        <a href="contact.php" class="text-white"><i class="fa-solid fa-question"></i> Help</a>
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
</style>