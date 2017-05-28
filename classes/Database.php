<?php

/**
 * class Database
 */
class Database
{
    /**
     * @var mysqli подключение к базе дынных
     */
    private $link;
    /**
     * @var string содержит ошибку подключения к б/д
     */
    private $error;

    /**
     * Фукция создает единственный объект подключения к базе данных
     * @return Database
     */
    public static function instance()
    {
        static $instance;
        if (!$instance) {
            $instance = new Database();
        }
        return $instance;
    }

    /**
     * Database constructor.
     * Открывает соединение с б/д
     */
    private function __construct()
    {
        include dirname(__FILE__).'/../config.php';
        /** @var array $DBCONFIG */
        $this->link = @mysqli_connect($DBCONFIG['host'], $DBCONFIG['user'], $DBCONFIG['password'], $DBCONFIG['database']);
        if (!$this->link) {
            $this->error = 'Ошибка: Невозможно подключиться к MySQL. ' . mysqli_connect_error();
            echo $this->error;
            exit;
        }
    }

    /**
     * Функция получет ошибку соединения
     * @return string
     */
    public function lastError()
    {
        return $this->error;
    }

    /**
     * Database destructor.
     * Закрывает соединение с б/д
     */
    public function __destruct()
    {
        mysqli_close($this->link);
    }

    /**
     * Функция обновляет данные в базе
     * @param string $nameTable -  имя таблицы
     * @param array $updateData ассоциативный массив, где ключ имя поля, значание данные для этого поля
     * @param array $where ассоциативный массив для условия обновления
     * @return int число обновленных записей и false при ошибке
     */
    public function updateData($nameTable, $updateData = [], $where = [])
    {
        $setPoints = Database::convertAssocDataToWhereStmt($updateData);
        $condition = Database::convertAssocDataToWhereStmt($where);

        $sql = 'UPDATE ' . $nameTable . ' SET ' . $setPoints[0] . ' WHERE ' . $condition[0];
        $value = array_merge($setPoints[1], $condition[1]);
        $stmt = db_get_prepare_stmt($this->link, $sql, $value);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return mysqli_affected_rows($this->link);
        }
        return false;
    }

    /**
     * Функция добавляет данные в базу
     * @param string $sql - sql запрос
     * @param array $data массив с данными для запроса
     * @return int последняя добавленная запись и false  при ошибке
     */
    public function setData($sql, $data = [])
    {
        $stmt = db_get_prepare_stmt($this->link, $sql, $data);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return mysqli_insert_id($this->link);
        }
        return false;
    }

    /**
     * Функция получает данные из базы
     * @param string $sql - sql запрос
     * @param array $data массив с данными для запроса
     * @return array $theResult  пустой массив или  // или что?
     */
    public function getData($sql, $data = [])
    {
        $stmt = db_get_prepare_stmt($this->link, $sql, $data);
        if ($stmt) {
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            if (!mysqli_num_rows($result)) {
                return [];
            }
            $theResult = [];
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $theResult[] = $row;
            }
            mysqli_stmt_close($stmt);
            return $theResult;
        }
        return [];
    }

    /**
     * Функция удаляет данные из базы
     * @param string $sql - sql запрос
     * @param array $data массив с данными для запроса
     * @return int|bool
     */
    public function deleteData($sql, $data = [])
    {
        $stmt = db_get_prepare_stmt($this->link, $sql, $data);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
            return mysqli_insert_id($this->link);
        }
        return false;
    }

    /**
     * Функция разбивает массив на 2 по ключу и значению
     * @param  array $array массив для преобразования
     * @return array , значениями которого я вляются 2 массива,
     * 1 - это строка с плайсхолдерами, 2 строка со значениями для них
     */
    public static function convertAssocDataToWhereStmt($array = [])
    {
        if (!$array) {
            return [];
        }
        $result = array_map(function ($k) {
            return $k . ' ?';
        }, array_keys($array));

        return [implode(", ", $result), array_values($array)];
    }
}