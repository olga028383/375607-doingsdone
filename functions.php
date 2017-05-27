<?php

require_once 'mysql_helper.php';
/* * Функция печатает тег head */

function printHead()
{
    print("<!DOCTYPE html>
        <html lang='en'>
        
        <head>
            <meta charset='UTF-8'>
            <title>Дела в Порядке!</title>
            <link rel='stylesheet' href='css/normalize.css'>
            <link rel='stylesheet' href='css/style.css'>
        </head>");
}

/* * Функция печатает закрывающий тег body, html, скрипты  */

function printEndBodyHtml()
{
    print("<script type='text/javascript' src='js/script.js'></script></body></html>");
}

/**
 * Функция проверяет корректность даты заполняемой пользователем
 * @param  string $str введенная дата
 * @return time() или false
 */
function checkForDateCorrected($str)
{
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
    $pattern = '((\d{2}\.\d{2}\.\d{4})|' . implode('|', array_keys($translate)) . ')(\s+в\s+((\d{2}):(\d{2})))?';
    $matches = [];
    $matched = preg_match("/^$pattern$/", $str, $matches);
    if (!$matched) {
        return false;
    }
    if (isset($matches[5]) && (int)$matches[5] > 23) {
        return false;
    }
    if (isset($matches[6]) && (int)$matches[6] > 59) {
        return false;
    }
    $date = $matches[1];
    $time = isset($matches[4]) ? $matches[4] : null;
    $seconds = $time ? strtotime("1970-01-01 $time UTC") : 0;
    if (isset($translate[$date])) {
        $resultTimestamp = $translate[$date] + $seconds;
    } else {
        $resultTimestamp = strtotime($date) + $seconds;
    }
    return $resultTimestamp >= strtotime('24:00:00') ? $resultTimestamp : false;
}

/**
 * Функция получает проекты
 * @param  array $user массив с данными о пользователе
 * @return array массив проектов из базы, соответствующий авторизованному пользователю
 */
function getProjects($user)
{
    $sql = "SELECT id, name FROM `projects` WHERE user_id = ?";
    return Database::instance()->getData($sql, [$user['id']]);
}

/**
 * Функция получает задачи, id проектов, файл, id и метки для задач (выполнена или нет)
 * @param  array $user даные авторизованного пользователя
 * @return array массив задач и проектов, соответствующих авторизованному пользователю
 */
function getTasksByProject($user)
{
    $sqlGetTasks = "SELECT name as task, deadline, project_id as project, id, file, complete FROM tasks WHERE user_id = ?";
    return Database::instance()->getData($sqlGetTasks, [$user['id']]);
}

/**
 * Функция получает задачу
 * @param $id идентификатор задачи
 * @param $user массив авторизованного пользователя
 * @return array|null
 */
function getTaskById($id, $user)
{
    $sqlGetTasks = "SELECT * FROM tasks WHERE user_id = ? AND id = ?";
    $res = Database::instance()->getData($sqlGetTasks, [$user['id'], $id]);
    return empty($res) ? null : $res[0];
}

/**
 * Функция удаляет задачу из базы
 * @param $id идентификатор задачи
 * @param $user массив авторизованного пользователя
 */
function deleteTaskById($id, $user)
{
    $sqlDeleteTasks = "DELETE FROM tasks WHERE user_id = ? AND id = ?";
    Database::instance()->deleteData($sqlDeleteTasks, [$user['id'], $id]);
}

/**
 * Функция добавляет задачу в базу
 * @param  array $resultAddTask валидные и не валидные поля
 * @param  string $pathFile путь к файлу либо null
 * @param  array $user данные авторизованного пользователя
 */
function addTaskToDatabase($resultAddTask, $pathFile, $user)
{
    $file = ($pathFile) ? '/upload/' . $pathFile : '';
    $user_id = $user['id'];
    $project_id = (int)$resultAddTask['project'];
    $deadline = date("Y-m-d H:i:s", checkForDateCorrected($resultAddTask['deadline']));
    $name = $resultAddTask['task'];
    $sqlAddTask = "INSERT INTO tasks(user_id, project_id, created, deadline, name, file) VALUES ( ?, ?, NOW(), ?, ?, ?)";
    Database::instance()->setData($sqlAddTask, [$user_id, $project_id, $deadline, $name, $file]);
}

/**
 * Функция копирует задачу
 * @param  array $task задача для копирования
 */
function duplicateTaskToDatabase($task)
{
    $sqlAddTask = "INSERT INTO tasks(user_id, project_id, created, deadline, name, file) VALUES ( ?, ?, NOW(), ?, ?, ?)";
    Database::instance()->setData($sqlAddTask, [$task['user_id'], $task['project_id'], $task['deadline'], $task['name'], $task['file']]);
}

/**
 * Функция добавляет категорию в базу
 * @param  array $resultCategoryTask валидные и не валидные поля
 * @param array $user данные авторизованного пользователя
 */
function addCategoryToDatabase($resultCategoryTask, $user)
{
    $user_id = $user['id'];
    $name = $resultCategoryTask['valid']['task'];
    $sqlAddTask = "INSERT INTO projects(user_id, name) VALUES ( ?, ?)";
    Database::instance()->setData($sqlAddTask, [$user_id, $name]);
    header("Location: /index.php");
    exit();
}

/**
 * Функция разбивает массив на 2 по ключу и значению
 * @param  array $array массив для преобразования
 * @return array , значениями которого я вляются 2 массива,
 * 1 - это строка с плайсхолдерами, 2 строка со значениями для них
 */
function convertAssocDataToWhereStmt($array = [])
{
    if (!$array) {
        return [];
    }
    $result = array_map(function ($k) {
        return $k . ' ?';
    }, array_keys($array));

    return [implode(", ", $result), array_values($array)];
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
 * @param string $text
 */
function addRequiredSpan($text = '')
{
    if ($text) {
        print("<p class='form__message'>$text</p>");
    }
}

/**
 * Функция устанавливает стиль для незаполненных поле формы.
 * @param array $errors - массив с ошибками
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
            $countTask++;
        }
    }
    return $countTask;
}
