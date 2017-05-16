<?php

/**
 * Функция проверяет корректность даты заполняемой пользователем
 * @param  string $str введенная дата
 * @return time() или false
 */
function checkForDateCorrected($str)
{
    //Как проверить еще со временем? и вопрос не по теме функции, как отловить нажатый чекбокс у задачи, чтобы отметить ее как выполненную?
    $translate = [
        'Сегодня' => time(),
        'Завтра' => time() + 86400,
        'Послезавтра' => time() + 172800,
        'Понедельник' => strtotime('Monday'),
        'Вторник' => strtotime('Tuesday'),
        'Среда' => strtotime('Wednesday'),
        'Четверг' => strtotime('Thursday'),
        'Пятница' => strtotime('Friday'),
        'Суббота' => strtotime('Saturday'),
        'Воскресенье' => strtotime('Sunday')
    ];
    return (isset($translate[$str]) && $translate[$str] >= strtotime('24:00:00')) ? $translate[$str] : false;
}

/**
 * Функция устанавливает метки для выполненных задач
 * @param  boolean $dbConnection результат соединения
 */
function updateForTasksFieldComplete($dbConnection)
{
    updateData($dbConnection, 'tasks', ['complete = ' => date("Y-m-d H:i:s", time())], ['deadline <' => date("Y-m-d H:i:s", strtotime('23:59:59'))]);
}

/**
 * Функция получает проекты
 * @param  boolean $dbConnection результат соединения
 * @param  array $user массив с данными о пользователе
 * @return array массив проектов из базы, соответствующий авторизованному пользователю
 */
function getProjects($dbConnection, $user)
{
    $sql = "SELECT id, name FROM `projects` WHERE user_id = ?";
    return getData($dbConnection, $sql, [$user['id']]);
}

/**
 * Функция получает задачи, id проектов и метки для задач (выполнена или нет)
 * @param  boolean $dbConnection результат соединения
 * @return array массив задач и проектов, соответствующих авторизованному пользователю
 */
function getTasksByProject($dbConnection, $user)
{
    $sqlGetTasks = "SELECT name as task, deadline, project_id as project, complete FROM tasks WHERE user_id = ?";
    return getData($dbConnection, $sqlGetTasks, [$user['id']]);
}

/**
 * Функция добавляет пользователей в базу
 * @param  boolean $dbConnection результат соединения
 * @param  array $resultRegister валидные и не валидные поля
 */
function addUserToDatabase($dbConnection, $resultRegister)
{
    $sqlAddUser = "INSERT INTO user(registered, email, name, password) VALUES (NOW(), ?, ?, ?)";
    setData($dbConnection, $sqlAddUser, [
        $resultRegister['output']['valid']['email'],
        $resultRegister['output']['valid']['name'],
        password_hash($resultRegister['output']['valid']['password'], PASSWORD_DEFAULT)]);
}

/**
 * Функция добавляет задачу в базу
 * @param  boolean $dbConnection результат соединения
 * @param  array $resultRegister валидные и не валидные поля
 * @param  array $file  путь к файлу если передан
 */
/* ВОт здесь неправильно как-то файл передается */
function addTaskToDatabase($dbConnection, $resultAddTask, $pathFile, $user)
{
    $file = ($pathFile !== null) ? $pathFile : '';
    $user_id = $user['id'];
    $project_id = (int) $resultAddTask['valid']['project'];
    $deadline = date("Y-m-d H:i:s", checkForDateCorrected($resultAddTask['valid']['deadline']));
    $name = $resultAddTask['valid']['task'];
    $sqlAddTask = "INSERT INTO tasks(user_id, project_id, created, deadline, name, file) VALUES ( ?, ?, NOW(), ?, ?, ?)";
    setData($dbConnection, $sqlAddTask, [$user_id, $project_id, $deadline, $name, $file]);
}

/**
 * Функция разбивает массив на 2 по ключу и значению
 * @param  array $array массив для преобразования
 *  @param string $condition знак для условия обновления
 * @return array , значениями которого я вляются 2 массива,
 * 1 - это строка с плайсхолдерами, 2 строка со значениями для них
 */
function convertAssocDataToWhereStmt($array = [])
{
    if (!$array) {
        return [];
    }
    $result = array_map(function($k) {
        return $k . ' ?';
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
    $setPoints = convertAssocDataToWhereStmt($updateData);
    $condition = convertAssocDataToWhereStmt($where);

    $sql = 'UPDATE ' . $nameTable . ' SET ' . $setPoints[0] . ' WHERE ' . $condition[0];
    $value = array_merge($setPoints[1], $condition[1]);
    $stmt = db_get_prepare_stmt($connectDB, $sql, $value);
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return mysqli_affected_rows($connectDB);
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
        mysqli_stmt_close($stmt);
        return mysqli_insert_id($connectDB);
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
        mysqli_stmt_close($stmt);
        return $theResult;
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
 * Функция находит пользователя по email в базе данных
 * @param array $dbConnection соединение с базой данных
 * @param string email электронная почта
 * @return array $user если пользоваетель существует или null 
 */
function searchUserByEmail($email, $dbConnection)
{
    $sql = "SELECT id, email, password, name FROM user WHERE email = ?";
    return getData($dbConnection, $sql, [$email]);
}

/**
 * Функция проверяет наличие заполненных полей и записывет значение в массив валидации,
 * @param array $fields  поля которые требуется проверять на валидность
 * @param array $dbConnection соединение с б/д
 * @return array[
 *      'error'=>bool,
 *      'output' => array[
 *          'valid' => array,
 *          'errors' => array
 *      ]
 *      'user' => array
 */
function validateLoginForm($dbConnection, $fields)
{
    $errors = false;
    $user = null;
    $output = AddkeysForValidation($fields);
    $user = searchUserByEmail($_POST['email'], $dbConnection);
    foreach ($fields as $name) {
        //Для поля email одна проверка, для отальных другая
        $validateTestFields = ($name == 'email') ?
                filter_var(sanitizeInput($_POST[$name]), FILTER_VALIDATE_EMAIL) :
                sanitizeInput($_POST[$name]);
        if (!empty($_POST[$name]) && $validateTestFields) {
            $output['valid'][$name] = $_POST[$name];
        } else {
            $output['errors'][$name] = true;
            $errors = true;
        }
    }
    return ['error' => $errors, 'output' => $output, 'user' => $user];
}

/**
 * Функция проверяет наличие заполненных полей и записывет значение в массив валидации
 * @param array $fields  поля которые требуется проверять на валидность
 * @return array[
 *      'error'=>bool,
 *      'valid' => array,
 *      'errors' => array
 */
function validateTaskForm($fields)
{
    $errors = false;
    $output = AddkeysForValidation($fields);
    foreach ($fields as $name) {
        //Для поля deadline одна проверка, для отальных другая
        $testValuePost = ($name == 'deadline') ?
                checkForDateCorrected(sanitizeInput($_POST[$name])) :
                sanitizeInput($_POST[$name]);
        if (!empty($_POST[$name]) && $testValuePost) {
            $output['valid'][$name] = $_POST[$name];
        } else {
            $output['errors'][$name] = true;
            $errors = true;
        }
    }
    return ['error' => $errors, 'valid' => $output['valid'], 'errors' => $output['errors']];
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
            print("<p class='form__message'>$text</p>");
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
 * @param string $idCategory - имя категории
 * @return int 
 */
function getNumberTasks($taskList, $idCategory)
{
    if ($idCategory == '0') {
        return count($taskList);
    }

    $countTask = 0;
    foreach ($taskList as $key => $value) {
        if ($value["project"] == $idCategory) {
            $countTask ++;
        }
    }
    return $countTask;
}
