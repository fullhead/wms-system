<?php
    $page_title = 'Добавить группу пользователей';
    require_once('includes/load.php');
    // // Проверяем, у пользователя какого уровня есть разрешение на просмотр этой страницы
    page_require_level(1);
?>
<?php
    if (isset($_POST['add'])) {

        $req_fields = array('group-name', 'group-level');
        validate_fields($req_fields);

        if (find_by_groupName($_POST['group-name']) === false) {
            $session->msg('d', '<b>Извините!</b> Введенное название группы уже есть в базе данных!');
            redirect('add_group.php', false);
        } elseif (find_by_groupLevel($_POST['group-level']) === false) {
            $session->msg('d', '<b>Извините!</b> Введенный уровень группы уже есть в базе данных!');
            redirect('add_group.php', false);
        }
        if (empty($errors)) {
            $name = remove_junk($db->escape($_POST['group-name']));
            $level = remove_junk($db->escape($_POST['group-level']));
            $status = remove_junk($db->escape($_POST['status']));

            $query = "INSERT INTO user_groups (";
            $query .= "group_name,group_level,group_status";
            $query .= ") VALUES (";
            $query .= " '{$name}', '{$level}','{$status}'";
            $query .= ")";
            if ($db->query($query)) {
                //sucess
                $session->msg('s', "Группа создана! ");
                redirect('add_group.php', false);
            } else {
                //failed
                $session->msg('d', ' Извините, не удалось создать группу!');
                redirect('add_group.php', false);
            }
        } else {
            $session->msg("d", $errors);
            redirect('add_group.php', false);
        }
    }
?>
<?php include_once('layouts/header.php'); ?>
<div class="login-page">
    <div class="text-center">
        <h3>Добавить новую группу пользователей</h3>
    </div>
    <?php echo display_msg($msg); ?>
    <form method="post" action="add_group.php" class="clearfix">
        <div class="form-group">
            <label for="name" class="control-label">Название группы</label>
            <input type="name" class="form-control" name="group-name">
        </div>
        <div class="form-group">
            <label for="level" class="control-label">Уровень группы</label>
            <input type="number" class="form-control" name="group-level">
        </div>
        <div class="form-group">
            <label for="status">Статус</label>
            <select class="form-control" name="status">
                <option value="1">Активный</option>
                <option value="0">Не активный</option>
            </select>
        </div>
        <div class="form-group clearfix">
            <button type="submit" name="add" class="btn btn-info">Добавить</button>
        </div>
    </form>
</div>

<?php include_once('layouts/footer.php'); ?>
