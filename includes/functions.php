<?php
    $errors = array();

    /*--------------------------------------------------------------*/
    /* Функция для удаления экранирует специальные символы в строке для использования в инструкции SQL
    /*--------------------------------------------------------------*/
    function real_escape($str)
    {
        global $con;
        $escape = mysqli_real_escape_string($con, $str);
        return $escape;
    }

    /*--------------------------------------------------------------*/
    /* Функция для удаления html-символов
    /*--------------------------------------------------------------*/
    function remove_junk($str)
    {
        $str = nl2br($str);
        $str = htmlspecialchars(strip_tags($str, ENT_QUOTES));
        return $str;
    }

    /*--------------------------------------------------------------*/
    /* Функция для ввода первого символа в верхнем регистре
    /*--------------------------------------------------------------*/
    function first_character($str)
    {
        $val = str_replace('-', " ", $str);
        $val = ucfirst($val);
        return $val;
    }

    /*--------------------------------------------------------------*/
    /* Функция для проверки того, что поля ввода не пустые
    /*--------------------------------------------------------------*/
    function validate_fields($var)
    {
        global $errors;
        $field_names_in_russian = array(
            'product-title' => 'Название товара',
            'product-categorie' => 'Категория товара',
            'product-quantity' => 'Количество товара',
            'buying-price' => 'Цена покупки',
            'saleing-price' => 'Цена продажи',
            's_id' => 'ID товара',
            'quantity' => 'Количество',
            'price' => 'Розничная цена',
            'total' => 'Итого',
            'date' => 'Дата',
            // добавьте сюда другие поля и их русские эквиваленты
        );
        foreach ($var as $field) {
            $val = remove_junk($_POST[$field]);
            if (isset($val) && $val == '') {
                $errors = $field_names_in_russian[$field] . " Заполните поля!";
                return $errors;
            }
        }
    }

    /*--------------------------------------------------------------*/
    /* Функция для отображения сообщения о сеансе
       Например, echo displayt_msg($сообщение);
    /*--------------------------------------------------------------*/
    function display_msg($msg = '')
    {
        $output = array();
        if (!empty($msg)) {
            foreach ($msg as $key => $value) {
                $output = "<div class=\"alert alert-{$key}\">";
                $output .= "<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>";
                $output .= remove_junk(first_character($value));
                $output .= "</div>";
            }
            return $output;
        } else {
            return "";
        }
    }

    /*--------------------------------------------------------------*/
    /* Функция для перенаправления
    /*--------------------------------------------------------------*/
    function redirect($url, $permanent = false)
    {
        if (headers_sent() === false) {
            header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
        }

        exit();
    }

    /*--------------------------------------------------------------*/
    /* Функция для определения общей цены продажи, цены покупки и прибыли
    /*--------------------------------------------------------------*/
    function total_price($totals)
    {
        $sum = 0;
        $sub = 0;
        foreach ($totals as $total) {
            $sum += $total['total_saleing_price'];
            $sub += $total['total_buying_price'];
            $profit = $sum - $sub;
        }
        return array($sum, $profit);
    }

    /*--------------------------------------------------------------*/
    /* Функция для считывания даты и времени
    /*--------------------------------------------------------------*/
    function read_date($str)
    {
        if ($str)
            return date('d.m.Y, H:i:s', strtotime($str));
        else
            return null;
    }

    /*--------------------------------------------------------------*/
    /* Функция для считывания даты и времени изготовления
    /*--------------------------------------------------------------*/
    function make_date()
    {
        return strftime("%Y-%m-%d %H:%M:%S", time());
    }

    /*--------------------------------------------------------------*/
    /* Функция для считывания даты и времени
    /*--------------------------------------------------------------*/
    function count_id()
    {
        static $count = 1;
        return $count++;
    }

    /*--------------------------------------------------------------*/
    /* Функция для создания случайной строки
    /*--------------------------------------------------------------*/
    function randString($length = 5)
    {
        $str = '';
        $cha = "0123456789abcdefghijklmnopqrstuvwxyz";

        for ($x = 0; $x < $length; $x++)
            $str .= $cha[mt_rand(0, strlen($cha))];
        return $str;
    }

