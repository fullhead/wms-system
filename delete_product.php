<?php
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(2);
?>
<?php
    $product = find_by_id('products', (int)$_GET['id']);
    if (!$product) {
        $session->msg("d", "Отсутствует ID товара!");
        redirect('product.php');
    }
?>
<?php
    $delete_id = delete_by_id('products', (int)$product['id']);
    if ($delete_id) {
        $session->msg("s", "Товар успешно уделен.");
        redirect('product.php');
    } else {
        $session->msg("d", "Ошибка при удаление товара.");
        redirect('product.php');
    }
?>
