<?php
    // Path: includes/database.php
    require_once(LIB_PATH_INC . DS . "config.php");

    // Подключение к базе данных
    class MySqli_DB
    {

        private $con;

        public $query_id;

        function __construct()
        {
            $this->db_connect();
        }

        /*--------------------------------------------------------------*/
        /* Функция для подключения к открытой базе данных
        /*--------------------------------------------------------------*/
        public function db_connect()
        {
            $this->con = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
            if (!$this->con) {
                die(" Database connection failed:" . mysqli_connect_error());
            } else {
                $select_db = $this->con->select_db(DB_NAME);
                if (!$select_db) {
                    die("Failed to Select Database" . mysqli_connect_error());
                }
            }
        }
        /*--------------------------------------------------------------*/
        /* Функция для тесного подключения к базе данных
        /*--------------------------------------------------------------*/

        public function db_disconnect()
        {
            if (isset($this->con)) {
                mysqli_close($this->con);
                unset($this->con);
            }
        }
        /*--------------------------------------------------------------*/
        /* Функция для запроса mysql
        /*--------------------------------------------------------------*/
        public function query($sql)
        {

            if (trim($sql != "")) {
                $this->query_id = $this->con->query($sql);
            }
            if (!$this->query_id)
                // only for Developer mode
                die("Error on this Query :<pre> " . $sql . "</pre>");
            // For production mode
            //  die("Error on Query");

            return $this->query_id;

        }

        /*--------------------------------------------------------------*/
        /* Функция для помощника по запросам
        /*--------------------------------------------------------------*/
        public function fetch_array($statement)
        {
            return mysqli_fetch_array($statement);
        }

        public function fetch_object($statement)
        {
            return mysqli_fetch_object($statement);
        }

        public function fetch_assoc($statement)
        {
            return mysqli_fetch_assoc($statement);
        }

        public function num_rows($statement)
        {
            return mysqli_num_rows($statement);
        }

        public function insert_id()
        {
            return mysqli_insert_id($this->con);
        }

        public function affected_rows()
        {
            return mysqli_affected_rows($this->con);
        }
        /*--------------------------------------------------------------*/
        /* Функция для удаления экранирующих элементов специальные
        /* символы в строке для использования в инструкции SQL
        /*--------------------------------------------------------------*/
        public function escape($str)
        {
            return $this->con->real_escape_string($str);
        }
        /*--------------------------------------------------------------*/
        /* Функция для цикла while
        /*--------------------------------------------------------------*/
        public function while_loop($loop)
        {
            global $db;
            $results = array();
            while ($result = $this->fetch_array($loop)) {
                $results[] = $result;
            }
            return $results;
        }

    }

    $db = new MySqli_DB();


