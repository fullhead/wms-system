<?php
    require_once('includes/load.php');
    //Проверяем, у пользователя какого уровня есть разрешение на просмотр этой страницы
    page_require_level(1);
?>
<?php
    $categorie = find_by_id('categories', (int)$_GET['id']);
    if (!$categorie) {
        $session->msg("d", "Отсутствует ID категории.");
        redirect('categorie.php');
    }
?>
<?php
    $delete_id = delete_by_id('categories', (int)$categorie['id']);
    if ($delete_id) {
        $session->msg("s", "Категория удалена.");
        redirect('categorie.php');
    } else {
        $session->msg("d", "Ошибка при удаление категории!");
        redirect('categorie.php');
    }
?>
