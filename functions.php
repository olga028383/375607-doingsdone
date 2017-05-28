<?php

require_once 'mysql_helper.php';
/* * Функция печатает тег head */

function printHead($bodyClassOverlay = '')
{
    print("<!DOCTYPE html>
        <html lang='en'>
        
        <head>
            <meta charset='UTF-8'>
            <title>Дела в Порядке!</title>
            <link rel='stylesheet' href='css/normalize.css'>
            <link rel='stylesheet' href='css/style.css'>
        </head>
        <body class=$bodyClassOverlay>
        <h1 class='visually-hidden'>Дела в порядке</h1>
        <div class='page-wrapper'>
            <div class='container container--with-sidebar'>");
}

/* * Функция печатает закрывающиеся теги   */

function printEndDivLayout()
{
    print("</div></div>");
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
        'сегодня' => strtotime('24:00:00'),
        'завтра' => time() + 86400,
        'послезавтра' => time() + 172800,
        'понедельник' => strtotime('Monday'),
        'вторник' => strtotime('Tuesday'),
        'среда' => strtotime('Wednesday'),
        'четверг' => strtotime('Thursday'),
        'пятница' => strtotime('Friday'),
        'суббота' => strtotime('Saturday'),
        'воскресенье' => strtotime('Sunday')
    ];
    $pattern = '((\d{2}\.\d{2}\.\d{4})|' . implode('|', array_keys($translate)) . ')(\s+в\s+((\d{2}):(\d{2})))?';
    $matches = [];
    $matched = preg_match("/^$pattern$/", mb_strtolower($str), $matches);
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
    require_once __DIR__ . "/templates/" . $template;

    return ob_get_clean();
}
