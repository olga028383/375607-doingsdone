<?php

/**
 * class Database
 */
class Database
{
    /**
     * @var подключение к базе дынных
     */
    private $link;
    /**
     * @var содержит ошибку подключения к б/д
     */
    private $error;

    /**
     * Фукция создает единственный объект подключения к базе данных
     * @return object
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
        include 'config.php';
        $this->link = mysqli_connect($DBCONFIG['host'], $DBCONFIG['user'], $DBCONFIG['password'], $DBCONFIG['database']);
        return ($this->link) ? $this->link : $this->error = 'Ошибка: Невозможно подключиться к MySQL ' . mysqli_connect_error();
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

}