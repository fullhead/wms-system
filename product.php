<?php
    $page_title = 'Изменить товар';
    require_once('includes/load.php');
    // Checkin What level user has permission to view this page
    page_require_level(2);
    $products = join_product_table();
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <div class="pull-right">
                    <a href="add_product.php" class="btn btn-primary">Добавить новый</a>
                </div>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th> Фото</th>
                        <th> Название товара</th>
                        <th class="text-center" style="width: 10%;"> Категория</th>
                        <th class="text-center" style="width: 10%;"> В наличии</th>
                        <th class="text-center" style="width: 10%;"> Цена покупки</th>
                        <th class="text-center" style="width: 10%;"> Цена продажи</th>
                        <th class="text-center" style="width: 10%;"> Товар добавлен</th>
                        <th class="text-center" style="width: 100px;"> Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td class="text-center"><?php echo count_id(); ?></td>
                            <td>
                                <?php if ($product['media_id'] === '0'): ?>
                                    <img class="img-avatar" src="uploads/products/no_image.png" alt="">
                                <?php else: ?>
                                    <img class="img-avatar"
                                         src="uploads/products/<?php echo $product['image']; ?>" alt="">
                                <?php endif; ?>
                            </td>
                            <td> <?php echo remove_junk($product['name']); ?></td>
                            <td class="text-center"> <?php echo remove_junk($product['categorie']); ?></td>
                            <td class="text-center"> <?php echo remove_junk($product['quantity']); ?></td>
                            <td class="text-center"> <?php echo remove_junk($product['buy_price']); ?></td>
                            <td class="text-center"> <?php echo remove_junk($product['sale_price']); ?></td>
                            <td class="text-center"> <?php echo read_date($product['date']); ?></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="edit_product.php?id=<?php echo (int)$product['id']; ?>"
                                       class="btn btn-info btn-xs" title="Изменить" data-toggle="tooltip">
                                        <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                    <a href="delete_product.php?id=<?php echo (int)$product['id']; ?>"
                                       class="btn btn-danger btn-xs" title="Удалить" data-toggle="tooltip">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include_once('layouts/footer.php'); ?>
