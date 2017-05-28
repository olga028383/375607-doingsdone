<?php

/**
 * class Auth
 */
class Auth
{
    /**
     * Функция возвращает массив пользователя, если он залогинен или пустой массив
     * @return array
     */
    public static function logout()
    {
        return (isset($_SESSION['user'])) ? $_SESSION['user'] : [];
    }

}
