<?php include_once('includes/load.php'); ?>
<?php
    $req_fields = array('username', 'password');
    validate_fields($req_fields);
    $username = remove_junk($_POST['username']);
    $password = remove_junk($_POST['password']);

    if (empty($errors)) {

        $user = authenticate_v2($username, $password);

        if ($user):
            //создать сеанс с идентификатором
            $session->login($user['id']);
            //Обновить время входа в систему
            updateLastLogIn($user['id']);
            // перенаправление пользователя на домашнюю страницу группы по уровню пользователя
            if ($user['user_level'] === '1'):
                $session->msg("s", "Hello " . $user['username'] . ", Welcome to OSWA-INV.");
                redirect('admin.php', false);
            elseif ($user['user_level'] === '2'):
                $session->msg("s", "Hello " . $user['username'] . ", Welcome to OSWA-INV.");
                redirect('special.php', false);
            else:
                $session->msg("s", "Hello " . $user['username'] . ", Welcome to OSWA-INV.");
                redirect('home.php', false);
            endif;

        else:
            $session->msg("d", "Sorry Username/Password incorrect.");
            redirect('index.php', false);
        endif;

    } else {

        $session->msg("d", $errors);
        redirect('login_v2.php', false);
    }

?>
