<?php
session_start();
require_once 'init.php';
$messageAfterRegistered = false;
$bodyClassOverlay = 'overlay';
//создаем объект для формы ауторизации
$authForm = new AuthForm();
//Если пользователь только что авторизовался, то покажем ему сообщение
if (isset($_GET['login']) && isset($_GET['show_message'])) {
    $messageAfterRegistered = true;
}
if ($authForm->isSubmitted()) {
    $authForm->validate();
    if ($authForm->isValid()) {
        if (password_verify($authForm->getDataField('password'), $authForm->user[0]['password'])) {
            $_SESSION['user'] = $authForm->user[0];
            header("Location: /index.php");
            exit();
        } else {
            $resultAuth['output']['errors']['password'] = true;
        }
    }
}
printHead($bodyClassOverlay);
print(includeTemplate('header.php', ['user' => Auth::logout()]));
print(includeTemplate('guest.php', []));
print(includeTemplate('login.php', [ 'form' => $authForm, 'messageAfterRegistered' => $messageAfterRegistered]));
printEndDivLayout();
print includeTemplate('footer.php', ['user' => Auth::logout()]);
printEndBodyHtml();