<?php
session_start();
require_once 'init.php';
$form = new SignupForm();
if ($form->isSubmitted()) {
    $form->validate();
    if ($form->isValid()) {
        User::addUser($form->getFormData());
        header("Location: /login.php?login=1&show_message=1");
    }
}
printHead();
print(includeTemplate('header.php', ['user' => Auth::getAuthUser()]));
print(includeTemplate('register.php', ['form' => $form]));
printEndDivLayout();
print includeTemplate('footer.php', ['user' => Auth::getAuthUser()]);
printEndBodyHtml();
