<?php

/**
 * class user
 */
class User
{
    /**
     * @var string $email
     */
    public $email;
    /**
     * @var string $password содержит hash пароля
     */
    public $password;
    /**
     * @var string $avatar
     */
    public $avatar;

    /**
     * Функция добавляет пользователей в базу
     * @param  array $fields валидные и не валидные поля
     */
    public static function addUser($fields)
    {
        $sqlAddUser = "INSERT INTO user(registered, email, name, password) VALUES (NOW(), ?, ?, ?)";
        Database::instance()->setData($sqlAddUser, [
            $fields['email'],
            $fields['name'],
            password_hash($fields['password'], PASSWORD_DEFAULT)]);
    }

    /**
     * Функция находит пользователя по email в базе данных
     * @param string $email электронная почта
     * @return array $user если пользоваетель существует или null
     */
    public static function getUser($email)
    {
        $sql = "SELECT id, email, password, name FROM user WHERE email = ?";
        $res = Database::instance()->getData($sql, [$email]);
        return count($res) > 0 ? $res[0] : null;
    }

}