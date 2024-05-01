<?php
    ob_start();
    require_once('includes/load.php');
    if ($session->isUserLoggedIn(true)) {
        redirect('home.php', false);
    }
?>
<?php include_once('layouts/header.php'); ?>
<div class="login-page">
    <div class="text-center">
        <h1>Авторизация</h1>
        <h4>WMS-система</h4>
        <br>
    </div>
    <?php echo display_msg($msg); ?>
    <form method="post" action="auth.php" class="clearfix">
        <div class="form-group">
            <label for="username" class="control-label">Введите имя пользователя</label>
            <input type="name" class="form-control" name="username" placeholder="Имя пользователя">
        </div>
        <div class="form-group">
            <label for="Password" class="control-label">Введите пароль</label>
            <input type="password" name="password" class="form-control" placeholder="Пароль">
        </div>
        <div class="form-group">
            <br>
            <button type="submit" class="btn btn-danger" style="border-radius:0">Вход</button>
        </div>
    </form>
</div>
<?php include_once('layouts/footer.php'); ?>
