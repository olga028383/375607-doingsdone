<?php
require_once 'functions.php';

$dbConnection = setConnection();

if (isset($_POST['register'])) {
    $resultRegister = validateLoginForm($dbConnection, ['email', 'name', 'password']);
    if (!$resultRegister['error']) {
        /* Функция добавляет пользователя в базу */
        addUserToDatabase($dbConnection, $resultRegister);
        header("Location: /index.php?login=1&show_message=1");
    } else {
        header("Location: /index.php?register=1");
    }
}