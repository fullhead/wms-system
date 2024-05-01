<?php
    $page_title = 'Изменить пароль';
    require_once('includes/load.php');
    // Проверяем, у пользователя какого уровня есть разрешение на просмотр этой страницы
    page_require_level(3);
?>
<?php $user = current_user(); ?>
<?php
    if (isset($_POST['update'])) {

        $req_fields = array('new-password', 'old-password', 'id');
        validate_fields($req_fields);

        if (empty($errors)) {

            if (sha1($_POST['old-password']) !== current_user()['password']) {
                $session->msg('d', "Ваш старый пароль не совпадает!");
                redirect('change_password.php', false);
            }

            $id = (int)$_POST['id'];
            $new = remove_junk($db->escape(sha1($_POST['new-password'])));
            $sql = "UPDATE users SET password ='{$new}' WHERE id='{$db->escape($id)}'";
            $result = $db->query($sql);
            if ($result && $db->affected_rows() === 1):
                $session->logout();
                $session->msg('s', "Войдите в систему со своим новым паролем.");
                redirect('index.php', false);
            else:
                $session->msg('d', ' Извините, не удалось изменить!');
                redirect('change_password.php', false);
            endif;
        } else {
            $session->msg("d", $errors);
            redirect('change_password.php', false);
        }
    }
?>
<?php include_once('layouts/header.php'); ?>
<div class="login-page">
    <div class="text-center">
        <h3>Измените свой пароль</h3>
    </div>
    <?php echo display_msg($msg); ?>
    <form method="post" action="change_password.php" class="clearfix">
        <div class="form-group">
            <label for="newPassword" class="control-label">Новый пароль</label>
            <input type="password" class="form-control" name="new-password" placeholder="Новый пароль">
        </div>
        <div class="form-group">
            <label for="oldPassword" class="control-label">Старый пароль</label>
            <input type="password" class="form-control" name="old-password" placeholder="Старый пароль">
        </div>
        <div class="form-group clearfix">
            <input type="hidden" name="id" value="<?php echo (int)$user['id']; ?>">
            <button type="submit" name="update" class="btn btn-info">Изменить</button>
        </div>
    </form>
</div>
<?php include_once('layouts/footer.php'); ?>
