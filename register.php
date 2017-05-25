<?php
require_once 'init.php';

$dbConnection = Database::instance();
/*$dataForRegisterTemplate = AddkeysForValidation(['email', 'name', 'password']);
if (isset($_POST['register'])) {
    $resultRegister = validateLoginForm($dbConnection, ['email', 'name', 'password']);
    if (!$resultRegister['error']) {
        addUserToDatabase($dbConnection, $resultRegister);
        header("Location: /index.php?login=1&show_message=1");
    }
    $dataForRegisterTemplate = $resultRegister;
}*/
$form = new SignupForm();
var_dump($_POST);
var_dump($form->isSubmitted());
if ($form->isSubmitted()) {
    $form ->validate();

}
printHead();
print(includeTemplate('register.php', ['errors' => $dataForRegisterTemplate['output']['errors'],
    'valid' => $dataForRegisterTemplate['output']['valid'],
    'user' => $dataForRegisterTemplate['user'], 'form' => $form]));
printEndBodyHtml();
