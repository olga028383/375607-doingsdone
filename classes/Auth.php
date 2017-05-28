<?php

/**
 * class Auth
 */
class Auth
{
    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public static function login($email, $password) {
        $user = User::getUser($email);
        $result = password_verify($password, $user['password']);
        if ($result) {
            $_SESSION['user'] = $user;
        }
        return $result;
    }

    public static function logout() {
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

    public static function requireAuthentication() {
        $user = self::getAuthUser();
        if (empty($user)) {
            header("Location: /login.php");
            exit;
        }
        return $user;
    }
}
