<?php

/**
 * class Auth
 */
class Auth
{
    /**
     * Функция создает сессию пользователя
     * @param string $email
     * @param string $password
     * @return bool
     */
    public static function login($email, $password)
    {
        $user = User::getUser($email);
        $result = password_verify($password, $user['password']);
        if ($result) {
            $_SESSION['user'] = $user;
        }
        return $result;
    }

    /**
     * Функция удаляет сессию
     */
    public static function logout()
    {
        session_unset();
        session_destroy();
    }

    /**
     * Функция возвращает массив пользователя, если он залогинен или пустой массив
     * @return array
     */
    public static function getAuthUser()
    {
        return isset($_SESSION['user']) ? $_SESSION['user'] : [];
    }
}
