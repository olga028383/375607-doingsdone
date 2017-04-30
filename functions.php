<?php
/**
 * Функция выводит блок с ошибкой.
 * @param boolean $errors  - ошибка пришла
 */
function addRequiredSpan($errors) {
    if (!empty($errors)) {
        print("<span>Обязательное поле</span>");
    }
}

/**
 * Функция устанавливает стиль для незаполненных поле формы.
 * @param boolean $fieldError  - ошибка пришла
 * @return string
 */
function setClassError($error) {
    return ($error) ? 'form__input--error' : '';
}

/**
 * Функция очищает входящие данные.
 * @param string $data принимает строку
 * @return string
 */
function checkInput($data) {
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
function includeTemplate($template, $templateData) {
    if (!isset($template)) {
        return "";
    }
    ob_start();

    /* htmlspecialcharacters() используется в шаблоне при выводе данных */
    require_once __DIR__ . "/templates/$template";

    $html = ob_get_clean();

    return $html;
}

/**
 * Функция считает количество задач.
 * @param array $taskList массив задач
 * @param string $nameCategory - имя категории
 * @return int 
 */
function getNumberTasks($taskList, $nameCategory) {
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
