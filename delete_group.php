<?php
    require_once('includes/load.php');
    // Проверяем, у пользователя какого уровня есть разрешение на просмотр этой страницы
    page_require_level(1);
?>
<?php
    $delete_id = delete_by_id('user_groups', (int)$_GET['id']);
    if ($delete_id) {
        $session->msg("s", "Группа успешна удалена.");
        redirect('group.php');
    } else {
        $session->msg("d", "Ошибка удаления группы Или отсутствует Prm.");
        redirect('group.php');
    }
?>
