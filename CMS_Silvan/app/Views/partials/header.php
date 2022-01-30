<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlatPeace - Peaceful Flat Sharing</title>
    <link rel="stylesheet" href="<?=$root?>/styles/main.css">
    <script defer src="https://kit.fontawesome.com/dd643b001e.js" crossorigin="anonymous"></script>
    <script defer src="<?=$root?>/js/menubar.js" type="text/javascript"></script>
</head>
<body>
    <header class="header col-12 bg-dark bg-gradient">
        <div id="logo-container">
            <a href="<?=$root?>/" class="d-flex">
                <img src="images/static/logo-transparent-white.png" alt="Logo FlatPeace">
                <p>FlatPeace</p>
            </a>
        </div>
        <div id="user-div">
            <link id="settings-icon" rel="shortcut icon" href="favicon.ico" type="image/x-icon">
            <?php if (!$user->isLoggedIn()): ?>
                <a href="<?=$root?>/register" id="sign-in" class="btn btn-primary">
                    Registrieren
                </a>
                <a href="<?=$root?>/login" id="login" class="btn btn-secondary">
                    Login
                </a>
            <?php else: ?>
                <a href="<?=$root?>/logout" id="logout" class="btn btn-secondary">
                    Logout
                </a>
            <?php endif; ?>
        </div>
    </header>
    <div id="page-wrapper">
        <aside id="menubar" class="bg-light fixed-top">
            <div id="slide-div">
                <div id="menu-toggle" class="bg-secondary btn btn-secondary d-flex justify-content-center align-items-center">
                <i class="fas fa-bars"></i>
                </div>
                <nav>
                    <ul class="ul-left">
                        <li class="li">
                            <a class="p-1 d-flex align-items-center" href="<?=$root?>/">
                                <i class="fa-left p-1 fas fa-home"></i>
                                <span class="d-none">Home</span>
                            </a>
                        </li>
                        <li class="li">
                            <a class="p-1 d-flex align-items-center" href="<?=$root?>/tasks">
                                <i class="fa-left p-1 fas fa-list-ul"></i>
                                <span class="d-none">Meine Tasks</span>
                            </a>
                        </li>
                        <li class="li">
                            <a class="p-1 d-flex align-items-center" href="<?=$root?>/write">
                                <i class="fa-left p-1 fas fa-plus-square"></i>
                                <span class="d-none">Task erstellen</span>
                            </a>
                        </li>
                        <li class="li">
                            <a class="p-1 d-flex align-items-center" href="<?=$root?>/done">
                                <i class="fa-left p-1 fas fa-check"></i>
                                <span class="d-none">Erledigte Tasks</span> 
                            </a>
                        </li>
                        <li class="li">
                            <a class="p-1 d-flex align-items-center" href="<?=$root?>/flat">
                                <i class="fa-left p-1 fas fa-users"></i>
                                <span class="d-none">Meine WG</span> 
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <div id="message-wrap" class="col">
            <?php if (!empty($_SESSION['message'])) : ?>
                <div class="messages alert alert-success mt-2 ms-2 me-2">
                    <?= $session::flash('message'); ?>
                </div>
            <?php endif; ?>
