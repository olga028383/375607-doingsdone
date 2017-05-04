<?php

/**
 * Функция находит пользователя по email массиве всех пользователей
 * @param array $users массив пользователей
 * @param string $email  эл. почта
 * @return $user если пользоваетель существует или null 
 */
function searchUserByEmail($email, $users)
{
    $result = null;
    foreach ($users as $user) {
        if ($user['email'] == $email) {
            $result = $user;
            break;
        }
    }
    return $result;
}

/**
 * Функция проверяет наличие заполненных полей и записывет значение в массив валидации,
 * если проверка на ауторизацию , то так же проверяет переданный email на наличие в массиве c пользователями
 * @param array $users  массив пользователей
 * @return array[
 *      'error'=>bool,
 *      'output' => array[
 *          'valid' => array,
 *          'errors' => array
 *      ]
 *      'user' => array
 */
function validateLoginForm($users)
{
    $errors = false;
    $user = null;
    $fields = ['email', 'password'];
    $output = AddkeysForValidation($fields);
    foreach ($fields as $name) {
        if (!empty($_POST[$name]) && $user = searchUserByEmail($_POST['email'], $users)) {
            $output['valid'][$name] = sanitizeInput($_POST[$name]);
        } else {
            $output['errors'][$name] = true;
            $errors = true;
        }
    }
    return ['error' => $errors, 'output' => $output, 'user' => $user];
}

/**
 * Функция инициализирует массивы валидных и не валидных полей и наполняет ключами из массива ожидаемых полей post- массива
 * @param array $keysField копируемый массив
 * @return возвращает заполненный массив
 */
function AddkeysForValidation($keysField)
{
    $result = [];
    foreach ($keysField as $field) {
        $result['valid'][$field] = '';
        $result['errors'][$field] = false;
    }
    return $result;
}

/**
 * Функция выводит блок с ошибкой.
 * @param array $errors  - массив с ошибками
 * @param string $name
 */
function addRequiredSpan($errors, $name, $text = '')
{
    if ($errors[$name]) {
        if ($text) {
            print("<p class='form__message'>$text</span>");
        } else {
            print("<span>Обязательное поле</span>");
        }
    }
}

/**
 * Функция устанавливает стиль для незаполненных поле формы.
 * @param array $errors  - массив с ошибками
 * @param string $name
 * @return string
 */
function setClassError($errors, $name)
{
    return ($errors[$name]) ? 'form__input--error' : '';
}

/**
 * Функция очищает входящие данные.
 * @param string $data принимает строку
 * @return string
 */
function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Функция печатает шаблон.
 * @param string $template имя шаблона
 * @param array $templateData - данные для шаблона
 * @return string
 */
function includeTemplate($template, $templateData)
{
    if (!isset($template)) {
        return "";
    }
    ob_start();

    /* htmlspecialcharacters() используется в шаблоне при выводе данных */
    require_once __DIR__ . "/templates/$template";

    return ob_get_clean();
}

/**
 * Функция считает количество задач.
 * @param array $taskList массив задач
 * @param string $nameCategory - имя категории
 * @return int 
 */
function getNumberTasks($taskList, $nameCategory)
{
    if (!$nameCategory) {
        return 0;
    }
    if ($nameCategory == "Все") {
        return count($taskList);
    }

    $countTask = 0;
    foreach ($taskList as $key => $value) {
        if ($value["project"] == $nameCategory) {
            $countTask ++;
        }
    }
    return $countTask;
}
