<?php
session_start();
require_once 'init.php';
$messageAfterRegistered = false;
//создаем объект для формы ауторизации
$authForm = new AuthForm();
//Если пользователь только что авторизовался, то покажем ему сообщение
if (isset($_GET['login']) && isset($_GET['show_message'])) {
    $messageAfterRegistered = true;
}
if ($authForm->isSubmitted()) {
    $authForm->validate();
    if ($authForm->isValid() ) {
        $data = $authForm->getFormData();
        if (Auth::login($data['email'], $data['password'])) {
            header("Location: /index.php");
            exit();
        } else {
            $authForm->addBadEmailOrPasswordError();
        }
    }
}

printHead(true);
print(includeTemplate('header.php', ['user' => Auth::getAuthUser()]));
print(includeTemplate('guest.php', []));
print(includeTemplate('login.php', [ 'form' => $authForm, 'messageAfterRegistered' => $messageAfterRegistered]));
printEndDivLayout();
print includeTemplate('footer.php', ['user' => Auth::getAuthUser()]);
printEndBodyHtml();