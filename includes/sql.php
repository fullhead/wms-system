<?php
    require_once('includes/load.php');

    /*--------------------------------------------------------------*/
    /* Функция для поиска всех строк таблицы базы данных по имени таблицы
    /*--------------------------------------------------------------*/
    function find_all($table)
    {
        global $db;
        if (tableExists($table)) {
            return find_by_sql("SELECT * FROM " . $db->escape($table));
        }
    }

    /*--------------------------------------------------------------*/
    /* Функция для выполнения запросов
    /*--------------------------------------------------------------*/
    function find_by_sql($sql)
    {
        global $db;
        $result = $db->query($sql);
        $result_set = $db->while_loop($result);
        return $result_set;
    }

    /*--------------------------------------------------------------*/
    /*  Функция для поиска данных из таблицы по идентификатору
    /*--------------------------------------------------------------*/
    function find_by_id($table, $id)
    {
        global $db;
        $id = (int)$id;
        if (tableExists($table)) {
            $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($id)}' LIMIT 1");
            if ($result = $db->fetch_assoc($sql))
                return $result;
            else
                return null;
        }
    }

    /*--------------------------------------------------------------*/
    /* Функция для удаления данных из таблицы по идентификатору
    /*--------------------------------------------------------------*/
    function delete_by_id($table, $id)
    {
        global $db;
        if (tableExists($table)) {
            $sql = "DELETE FROM " . $db->escape($table);
            $sql .= " WHERE id=" . $db->escape($id);
            $sql .= " LIMIT 1";
            $db->query($sql);
            return ($db->affected_rows() === 1) ? true : false;
        }
    }

    /*--------------------------------------------------------------*/
    /* Функция для подсчета идентификатора по имени таблицы
    /*--------------------------------------------------------------*/

    function count_by_id($table)
    {
        global $db;
        if (tableExists($table)) {
            $sql = "SELECT COUNT(id) AS total FROM " . $db->escape($table);
            $result = $db->query($sql);
            return ($db->fetch_assoc($result));
        }
    }

    /*--------------------------------------------------------------*/
    /* Определите, существует ли таблица базы данных
    /*--------------------------------------------------------------*/
    function tableExists($table)
    {
        global $db;
        $table_exit = $db->query('SHOW TABLES FROM ' . DB_NAME . ' LIKE "' . $db->escape($table) . '"');
        if ($table_exit) {
            if ($db->num_rows($table_exit) > 0)
                return true;
            else
                return false;
        }
    }

    /*--------------------------------------------------------------*/
    /* Вход в систему, используя данные, указанные в $_POST,
    /* из формы входа в систему.
   /*--------------------------------------------------------------*/
    function authenticate($username = '', $password = '')
    {
        global $db;
        $username = $db->escape($username);
        $password = $db->escape($password);
        $sql = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
        $result = $db->query($sql);
        if ($db->num_rows($result)) {
            $user = $db->fetch_assoc($result);
            $password_request = sha1($password);
            if ($password_request === $user['password']) {
                return $user['id'];
            }
        }
        return false;
    }

    /*--------------------------------------------------------------*/
    /* Вход в систему, используя данные, указанные в $_POST,
    /* /* из формы login_v2.php.
   /*--------------------------------------------------------------*/
    function authenticate_v2($username = '', $password = '')
    {
        global $db;
        $username = $db->escape($username);
        $password = $db->escape($password);
        $sql = sprintf("SELECT id,username,password,user_level FROM users WHERE username ='%s' LIMIT 1", $username);
        $result = $db->query($sql);
        if ($db->num_rows($result)) {
            $user = $db->fetch_assoc($result);
            $password_request = sha1($password);
            if ($password_request === $user['password']) {
                return $user;
            }
        }
        return false;
    }


    /*--------------------------------------------------------------*/
    /* Поиск текущего пользователя, вошедшего в систему, по идентификатору сеанса
    /*--------------------------------------------------------------*/
    function current_user()
    {
        static $current_user;
        global $db;
        if (!$current_user) {
            if (isset($_SESSION['user_id'])):
                $user_id = intval($_SESSION['user_id']);
                $current_user = find_by_id('users', $user_id);
            endif;
        }
        return $current_user;
    }

    /*--------------------------------------------------------------*/
    /* Найти всех пользователей по
    /* Объединение таблицы пользователей и таблицы групп пользователей
    /*--------------------------------------------------------------*/
    function find_all_user()
    {
        global $db;
        $results = array();
        $sql = "SELECT u.id,u.name,u.username,u.user_level,u.status,u.last_login,";
        $sql .= "g.group_name ";
        $sql .= "FROM users u ";
        $sql .= "LEFT JOIN user_groups g ";
        $sql .= "ON g.group_level=u.user_level ORDER BY u.name ASC";
        $result = find_by_sql($sql);
        return $result;
    }

    /*--------------------------------------------------------------*/
    /* Функция обновления данных о последнем входе пользователя в систему
    /*--------------------------------------------------------------*/

    function updateLastLogIn($user_id)
    {
        global $db;
        $date = make_date();
        $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
        $result = $db->query($sql);
        return ($result && $db->affected_rows() === 1 ? true : false);
    }

    /*--------------------------------------------------------------*/
    /* Найти название всей группы
    /*--------------------------------------------------------------*/
    function find_by_groupName($val)
    {
        global $db;
        $sql = "SELECT group_name FROM user_groups WHERE group_name = '{$db->escape($val)}' LIMIT 1 ";
        $result = $db->query($sql);
        return ($db->num_rows($result) === 0 ? true : false);
    }

    /*--------------------------------------------------------------*/
    /* Найти уровень группы
    /*--------------------------------------------------------------*/
    function find_by_groupLevel($level)
    {
        global $db;
        $sql = "SELECT group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
        $result = $db->query($sql);
        return ($db->num_rows($result) === 0 ? true : false);
    }

    /*--------------------------------------------------------------*/
    /* Функция для проверки того, какой уровень пользователя имеет доступ к странице
    /*--------------------------------------------------------------*/
    function page_require_level($require_level)
    {
        global $session;
        $current_user = current_user();
        $login_level = find_by_groupLevel($current_user['user_level']);
        //Если пользователь не вошел в систему
        if (!$session->isUserLoggedIn(true)):
            $session->msg('d', 'Пожалуйста, войдите в систему...');
            redirect('index.php', false);
        //Если статус группы неактивный
        elseif ($login_level['group_status'] === '0'):
            $session->msg('d', 'Доступ к вашей группе ограничен администратором!');
            redirect('home.php', false);
        //Проверка уровня пользователя для входа в систему и уровня требований
        elseif ($current_user['user_level'] <= (int)$require_level):
            return true;
        else:
            $session->msg("d", "Простите! У вас нет разрешения на просмотр этой страницы.");
            redirect('home.php', false);
        endif;
    }



    /*--------------------------------------------------------------*/
    /* Функция для поиска всех названий продуктов
    /* ОБЪЕДИНЕНИЕ с таблицей категорий и базы данных мультимедиа
    /*--------------------------------------------------------------*/
    function join_product_table()
    {
        global $db;
        $sql = " SELECT p.id,p.name,p.quantity,p.buy_price,p.sale_price,p.media_id,p.date,c.name";
        $sql .= " AS categorie,m.file_name AS image";
        $sql .= " FROM products p";
        $sql .= " LEFT JOIN categories c ON c.id = p.categorie_id";
        $sql .= " LEFT JOIN media m ON m.id = p.media_id";
        $sql .= " ORDER BY p.id ASC";
        return find_by_sql($sql);

    }

    /*--------------------------------------------------------------*/
    /* Функция для поиска всех названий продуктов
    /* Запрос поступает от ajax.php для автоматического предложения
    /*--------------------------------------------------------------*/

    function find_product_by_title($product_name)
    {
        global $db;
        $p_name = remove_junk($db->escape($product_name));
        $sql = "SELECT name FROM products WHERE name like '%$p_name%' LIMIT 5";
        $result = find_by_sql($sql);
        return $result;
    }

    /*--------------------------------------------------------------*/
    /* Функция поиска всей информации о продукте по названию продукта
    /* Запрос, поступающий от ajax.php
    /*--------------------------------------------------------------*/
    function find_all_product_info_by_title($title)
    {
        global $db;
        $sql = "SELECT * FROM products ";
        $sql .= " WHERE name ='{$title}'";
        $sql .= " LIMIT 1";
        return find_by_sql($sql);
    }

    /*--------------------------------------------------------------*/
    /* Функция обновления количества продукта
    /*--------------------------------------------------------------*/
    function update_product_qty($qty, $p_id)
    {
        global $db;
        $qty = (int)$qty;
        $id = (int)$p_id;
        $sql = "UPDATE products SET quantity=quantity -'{$qty}' WHERE id = '{$id}'";
        $result = $db->query($sql);
        return ($db->affected_rows() === 1 ? true : false);

    }

    /*--------------------------------------------------------------*/
    /* Добавлена функция отображения последнего продукта
    /*--------------------------------------------------------------*/
    function find_recent_product_added($limit)
    {
        global $db;
        $sql = " SELECT p.id,p.name,p.sale_price,p.media_id,c.name AS categorie,";
        $sql .= "m.file_name AS image FROM products p";
        $sql .= " LEFT JOIN categories c ON c.id = p.categorie_id";
        $sql .= " LEFT JOIN media m ON m.id = p.media_id";
        $sql .= " ORDER BY p.id DESC LIMIT " . $db->escape((int)$limit);
        return find_by_sql($sql);
    }

    /*--------------------------------------------------------------*/
    /* Функция поиска самого продаваемого продукта
    /*--------------------------------------------------------------*/
    function find_higest_saleing_product($limit)
    {
        global $db;
        $sql = "SELECT p.name, COUNT(s.product_id) AS totalSold, SUM(s.qty) AS totalQty";
        $sql .= " FROM sales s";
        $sql .= " LEFT JOIN products p ON p.id = s.product_id ";
        $sql .= " GROUP BY s.product_id";
        $sql .= " ORDER BY SUM(s.qty) DESC LIMIT " . $db->escape((int)$limit);
        return $db->query($sql);
    }

    /*--------------------------------------------------------------*/
    /* Функция поиска всех продаж
    /*--------------------------------------------------------------*/
    function find_all_sale()
    {
        global $db;
        $sql = "SELECT s.id,s.qty,s.price,s.date,p.name";
        $sql .= " FROM sales s";
        $sql .= " LEFT JOIN products p ON s.product_id = p.id";
        $sql .= " ORDER BY s.date DESC";
        return find_by_sql($sql);
    }

    /*--------------------------------------------------------------*/
    /* Функция отображения последних продаж
    /*--------------------------------------------------------------*/
    function find_recent_sale_added($limit)
    {
        global $db;
        $sql = "SELECT s.id,s.qty,s.price,s.date,p.name";
        $sql .= " FROM sales s";
        $sql .= " LEFT JOIN products p ON s.product_id = p.id";
        $sql .= " ORDER BY s.date DESC LIMIT " . $db->escape((int)$limit);
        return find_by_sql($sql);
    }

    /*--------------------------------------------------------------*/
    /* Функция для формирования отчета о продажах по двум датам
    /*--------------------------------------------------------------*/
    function find_sale_by_dates($start_date, $end_date)
    {
        global $db;
        $start_date = date("Y-m-d", strtotime($start_date));
        $end_date = date("Y-m-d", strtotime($end_date));
        $sql = "SELECT s.date, p.name,p.sale_price,p.buy_price,";
        $sql .= "COUNT(s.product_id) AS total_records,";
        $sql .= "SUM(s.qty) AS total_sales,";
        $sql .= "SUM(p.sale_price * s.qty) AS total_saleing_price,";
        $sql .= "SUM(p.buy_price * s.qty) AS total_buying_price ";
        $sql .= "FROM sales s ";
        $sql .= "LEFT JOIN products p ON s.product_id = p.id";
        $sql .= " WHERE s.date BETWEEN '{$start_date}' AND '{$end_date}'";
        $sql .= " GROUP BY DATE(s.date),p.name";
        $sql .= " ORDER BY DATE(s.date) DESC";
        return $db->query($sql);
    }

    /*--------------------------------------------------------------*/
    /* Функция для формирования ежедневного отчета о продажах
    /*--------------------------------------------------------------*/
    function dailySales($year, $month)
    {
        global $db;
        $sql = "SELECT s.qty,";
        $sql .= " DATE_FORMAT(s.date, '%Y-%m-%e') AS date,p.name,";
        $sql .= "SUM(p.sale_price * s.qty) AS total_saleing_price";
        $sql .= " FROM sales s";
        $sql .= " LEFT JOIN products p ON s.product_id = p.id";
        $sql .= " WHERE DATE_FORMAT(s.date, '%Y-%m' ) = '{$year}-{$month}'";
        $sql .= " GROUP BY DATE_FORMAT( s.date,  '%e' ),s.product_id";
        return find_by_sql($sql);
    }



    /*--------------------------------------------------------------*/
    /* Функция для формирования ежемесячного отчета о продажах
    /*--------------------------------------------------------------*/
    function monthlySales($year)
    {
        global $db;
        $sql = "SELECT s.qty,";
        $sql .= " DATE_FORMAT(s.date, '%Y-%m-%e') AS date,p.name,";
        $sql .= "SUM(p.sale_price * s.qty) AS total_saleing_price";
        $sql .= " FROM sales s";
        $sql .= " LEFT JOIN products p ON s.product_id = p.id";
        $sql .= " WHERE DATE_FORMAT(s.date, '%Y' ) = '{$year}'";
        $sql .= " GROUP BY DATE_FORMAT( s.date,  '%c' ),s.product_id";
        $sql .= " ORDER BY date_format(s.date, '%c' ) ASC";
        return find_by_sql($sql);
    }



