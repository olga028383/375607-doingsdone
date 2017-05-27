<?php
require_once 'init.php';
$form = new SignupForm();
if ($form->isSubmitted()) {
    $form->validate();
    if ($form->isValid()) {
        Auth::addUserToDatabase($form->getformData());
        header("Location: /index.php?login=1&show_message=1");
    }
}
printHead();
print(includeTemplate('register.php', ['form' => $form]));
printEndBodyHtml();
