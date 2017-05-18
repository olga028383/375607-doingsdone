<?php
require_once 'functions.php';

$dbConnection = setConnection();
$dataForRegisterTemplate = AddkeysForValidation(['email', 'name', 'password']);
if (isset($_POST['register'])) {
    $resultRegister = validateLoginForm($dbConnection, ['email', 'name', 'password']);
    if (!$resultRegister['error']) {
        /* Функция добавляет пользователя в базу */
        addUserToDatabase($dbConnection, $resultRegister);
        header("Location: /index.php?login=1&show_message=1");
    }
    $dataForRegisterTemplate = $resultRegister;
}
printHead();
print(includeTemplate('register.php', ['errors' => $dataForRegisterTemplate['output']['errors'],
    'valid' => $dataForRegisterTemplate['output']['valid'],
    'user' => $dataForRegisterTemplate['user']]));
printEndBodyHtml();
