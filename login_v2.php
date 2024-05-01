<?php
    ob_start();
    require_once('includes/load.php');
    if ($session->isUserLoggedIn(true)) {
        redirect('home.php', false);
    }
?>

<div class="login-page">
    <div class="text-center">
        <h1>Добро пожаловать</h1>
        <p>Войдите для начало сессии</p>
    </div>
    <?php echo display_msg($msg); ?>
    <form method="post" action="auth_v2.php" class="clearfix">
        <div class="form-group">
            <label for="username" class="control-label">Логин</label>
            <input type="name" class="form-control" name="username" placeholder="Логин">
        </div>
        <div class="form-group">
            <label for="Password" class="control-label">Пароль</label>
            <input type="password" name="password" class="form-control" placeholder="Пароль">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-info  pull-right">Вход</button>
        </div>
    </form>
</div>
<?php include_once('layouts/header.php'); ?>
