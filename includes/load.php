<?php
    // -----------------------------------------------------------------------
    // ОПРЕДЕЛЕНИЕ ПСЕВДОНИМОВ РАЗДЕЛИТЕЛЕЙ
    // -----------------------------------------------------------------------
    const URL_SEPARATOR = '/';

    const DS = DIRECTORY_SEPARATOR;

    // -----------------------------------------------------------------------
    // ОПРЕДЕЛИТЕ КОРНЕВЫЕ ПУТИ
    // -----------------------------------------------------------------------
    defined('SITE_ROOT') ? null : define('SITE_ROOT', realpath(dirname(__FILE__)));
    const LIB_PATH_INC = SITE_ROOT . DS;


    require_once(LIB_PATH_INC . 'config.php');
    require_once(LIB_PATH_INC . 'functions.php');
    require_once(LIB_PATH_INC . 'session.php');
    require_once(LIB_PATH_INC . 'upload.php');
    require_once(LIB_PATH_INC . 'database.php');
    require_once(LIB_PATH_INC . 'sql.php');

