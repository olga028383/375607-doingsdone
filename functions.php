<?php

/**
 * Функция разбивает массив на 2 по ключу и значению
 * @param  array $array массив для преобразования
 * @return array , значениями которого я вляются 2 массива,
 * 1 - это строка с плайсхолдерами, 2 строка со значениями для них
 */
function replaceKeyArray($array = [])
{
    if (!$array) {
        return;
    }
    $newKey = [];
    foreach ($array as $key => $value) {
        $newKey[] = $key . ' = ?';
    }
    $result = array_map(function($k) {
        return $k . ' = ?';
    }, array_keys($array));

    return [implode(", ", $result), array_values($array)];
}

/**
 * Функция обновляет данные в базе
 * @param  boolean $connectDB результат соединения
 * @param string $nameTable -  имя таблицы
 * @param array $updateData ассоциативный массив, где ключ имя поля, значание данные для этого поля
 * @param array $where ассоциативный массив для условия обновления
 * @return int число обновленных записей и false при ошибке
 */
function updateData($connectDB, $nameTable, $updateData = [], $where = [])
{
    $setPoints = replaceKeyArray($updateData);
    $condition = replaceKeyArray($where);

    $sql = 'UPDATE ' . $nameTable . ' SET ' . $setPoints[0] . ' WHERE ' . $condition[0];
    $value = array_merge($setPoints[1], $condition[1]);
    $stmt = db_get_prepare_stmt($connectDB, $sql, $value);
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        return mysqli_affected_rows($connectDB);
        mysqli_stmt_close();
    }
    return false;
}

/**
 * Функция добавляет данные в базу
 * @param  boolean $connectDB результат соединения
 * @param string $sql - sql запрос
 * @param array $data массив с данными для запроса
 * @return int последняя добавленная запись и false  при ошибке
 */
function setData($connectDB, $sql, $data = [])
{
    $stmt = db_get_prepare_stmt($connectDB, $sql, $data);
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        return mysqli_insert_id($connectDB);
        mysqli_stmt_close();
    }
    return false;
}

/**
 * Функция получает данные из базы
 * @param  boolean $connectDB результат соединения
 * @param string $sql - sql запрос
 * @param array $data массив с данными для запроса
 * @return array $theResult  пустой массив или 
 */
function getData($connectDB, $sql, $data = [])
{
    $stmt = db_get_prepare_stmt($connectDB, $sql, $data);
    if ($stmt) {
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if (!mysqli_num_rows($result)) {
            return [];
        }
        $theResult = [];
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $theResult[] = $row;
        }
        return $theResult;
        mysqli_stmt_close();
    }
    return [];
}

/**
 * Функция устанавливает соединение с базой данных
 * @param string $server адрес сервера
 * @param string $nameUser имя пользователя
 * @param string $password пароль пользователя б/д
 * @param string $nameDataBase пароль пользователя б/д
 * @return mysqli|string соединение если успешно, иначе сообщение об ошибке.
 */
function setConnection($server, $nameUser, $password, $nameDataBase)
{
    $result = [];
    $link = mysqli_connect($server, $nameUser, $password, $nameDataBase);
    return ($link) ? $link : 'Ошибка: Невозможно подключиться к MySQL ' . mysqli_connect_error();
}

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
