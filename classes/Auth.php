<?php

/**
 * class Auth
 */
class Auth
{
    /**
     * Функция добавляет пользователей в базу
     * @param  array $resultRegister валидные и не валидные поля
     */
    public static function addUserToDatabase($resultRegister)
    {
        $sqlAddUser = "INSERT INTO user(registered, email, name, password) VALUES (NOW(), ?, ?, ?)";
        Database::instance()->setData($sqlAddUser, [
            $resultRegister['email'],
            $resultRegister['name'],
            password_hash($resultRegister['password'], PASSWORD_DEFAULT)]);
    }

    /**
     * Функция находит пользователя по email в базе данных
     * @param string email электронная почта
     * @return array $user если пользоваетель существует или null
     */
    public static function getUser($email)
    {
        $sql = "SELECT id, email, password, name FROM user WHERE email = ?";
        return Database::instance()->getData($sql, [$email]);
    }

}
