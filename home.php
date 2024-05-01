<?php
    $page_title = 'WMS-система';
    require_once('includes/load.php');
    if (!$session->isUserLoggedIn(true)) {
        redirect('index.php', false);
    }
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
    <div class="col-md-12">
        <div class="panel">
            <div class="jumbotron text-center">
                <h1>Добро пожаловать!
                    <hr>
                    WMS-Система
                </h1>
                <p>Выберите нужные вам инструменты на панели управления!</p>
            </div>
        </div>
    </div>
</div>
<?php include_once('layouts/footer.php'); ?>
