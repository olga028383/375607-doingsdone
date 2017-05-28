<?php


/**
 * Функция добавляет категорию в базу
 * @param  array $resultCategoryTask валидные и не валидные поля
 * @param array $user данные авторизованного пользователя   ?????
 */
function addProject($resultCategoryTask, $user)
{
    $user_id = $user['id'];
    $name = $resultCategoryTask['category'];
    $sqlAddTask = "INSERT INTO projects(user_id, name) VALUES ( ?, ?)";
    Database::instance()->setData($sqlAddTask, [$user_id, $name]);
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