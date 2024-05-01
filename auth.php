

<?php include_once('includes/load.php'); ?>
<?php
    $req_fields = array('username', 'password');
    validate_fields($req_fields);
    $username = remove_junk($_POST['username']);
    $password = remove_junk($_POST['password']);
    // Сравнение полученных данных
    if (empty($errors)) {
        $user_id = authenticate($username, $password);
        if ($user_id) {
            //Создание сессии с ID пользователя
            $session->login($user_id);
            //Обновление данных времени входа пользователя
            updateLastLogIn($user_id);
            $session->msg("s", "Добро пожаловать в WMS-систему");
            redirect('admin.php', false);

        } else {
            $session->msg("d", "Извините, имя пользователя или пароль неверный.");
            redirect('index.php', false);
        }

    } else {
        $session->msg("d", $errors);
        redirect('index.php', false);
    }
?>


