<?php

/**
 * Class Task
 */
class Task
{
    /**
     * Функция получает задачу
     * @param int $id идентификатор задачи
     * @param array $user массив авторизованного пользователя
     * @return array|null
     */
    public static function getTaskById($id, $user)
    {
        $sqlGetTasks = "SELECT * FROM tasks WHERE user_id = ? AND id = ?";
        $res = Database::instance()->getData($sqlGetTasks, [$user['id'], $id]);
        return empty($res) ? null : $res[0];
    }

    /**
     * Функция удаляет задачу из базы
     * @param int $id идентификатор задачи
     * @param array $user массив авторизованного пользователя
     */
    public static function deleteTaskById($id, $user)
    {
        $sqlDeleteTasks = "DELETE FROM tasks WHERE user_id = ? AND id = ?";
        Database::instance()->deleteData($sqlDeleteTasks, [$user['id'], $id]);
    }

    /**
     * Функция получает задачи, id проектов, файл, id и метки для задач (выполнена или нет)
     * @param  array $user даные авторизованного пользователя
     * @return array массив задач и проектов, соответствующих авторизованному пользователю
     */
    public static function getTasksByProject($user)
    {
        $sqlGetTasks = "SELECT name as task, deadline, project_id as project, id, file, complete 
                        FROM tasks WHERE user_id = ?";
        return Database::instance()->getData($sqlGetTasks, [$user['id']]);
    }

    /**
     * Функция считает количество задач.
     * @param array $taskList массив задач
     * @param string $idCategory - имя категории
     * @return int
     */
    public static function getNumberTasks($taskList, $idCategory)
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

    /**
     * Функция выполняет sql запрос на получение данных в зависимости от переданных параметров
     * @param string $condition условие запроса
     * @param array $param параметры запроса
     * @return array массив задач, соответствующих авторизованному пользователю
     */
    public static function sqlOnRequestFilter($condition, $param = [])
    {
        $sqlGetTasks = "SELECT name as task, deadline, project_id as project, id, file, complete 
                        FROM tasks WHERE user_id = ? " . $condition;
        return Database::instance()->getData($sqlGetTasks, $param);
    }

    /**
     * Функция получает задачи, id проектов, файл, id и метки для задач (выполнена или нет) по поиску
     * @param array $user даные авторизованного пользователя
     * @param string $query запрос поиска
     * @return array массив задач и проектов, соответствующих авторизованному пользователю
     */
    public static function getTasksByProjectOnRequestFilter($user, $query)
    {
        switch ($query) {
            case 'today' :
                $condition = "AND deadline BETWEEN ? AND ?";
                $time_from = date('Y-m-d H:i:s', time());
                $time_to = date('Y-m-d H:i:s', strtotime("tomorrow"));
                return Task::sqlOnRequestFilter($condition, [$user['id'], $time_from, $time_to]);
                break;
            case 'tomorrow':
                $condition = "AND deadline BETWEEN ? AND ?";
                $time_from = date('Y-m-d H:i:s', strtotime("tomorrow"));
                $time_to = date('Y-m-d H:i:s', strtotime("tomorrow + 1 day"));
                return Task::sqlOnRequestFilter($condition, [$user['id'], $time_from, $time_to]);
                break;
            case 'overdue':
                $condition = "AND deadline < ? AND complete IS NULL";
                $time = date('Y-m-d H:i:s', time());
                return Task::sqlOnRequestFilter($condition, [$user['id'], $time]);
                break;
        }
    }

    /**
     * Функция получает задачи, id проектов, файл, id и метки для задач (выполнена или нет) по поиску
     * @param  array $user даные авторизованного пользователя
     * @param string $query запрос поиска
     * @return array массив задач и проектов, соответствующих авторизованному пользователю
     */
    public static function getTasksByProjectOnRequest($user, $query)
    {
        $sqlGetTasks = "SELECT name as task, deadline, project_id as project, id, file, complete 
                        FROM tasks WHERE user_id = ? AND `name` LIKE ?";
        return Database::instance()->getData($sqlGetTasks, [$user['id'], "%$query%"]);
    }

    /**
     * Функция добавляет задачу в базу
     * @param  array $resultAddTask валидные и не валидные поля
     * @param  string $nameFile имя файла либо null
     * @param  array $user данные авторизованного пользователя
     */
    public static function addTaskToDatabase($resultAddTask, $nameFile, $user)
    {
        $file = ($nameFile) ? '/upload/' . $nameFile : '';
        $user_id = $user['id'];
        $project_id = (int)$resultAddTask['project'];
        $deadline = checkForDateCorrected($resultAddTask['deadline']);
        $name = $resultAddTask['task'];
        $sqlAddTask = "INSERT INTO tasks(user_id, project_id, created, deadline, name, file) 
                       VALUES ( ?, ?, NOW(), ?, ?, ?)";
        Database::instance()->setData($sqlAddTask, [$user_id, $project_id, $deadline, $name, $file]);
    }

    /**
     * Функция копирует задачу
     * @param  array $task задача для копирования
     */
    public static function duplicateTaskToDatabase($task)
    {
        $sqlAddTask = "INSERT INTO tasks(user_id, project_id, created, deadline, name, file) 
                       VALUES ( ?, ?, NOW(), ?, ?, ?)";
        Database::instance()->setData($sqlAddTask, [
            $task['user_id'],
            $task['project_id'],
            $task['deadline'],
            $task['name'],
            $task['file']]);
    }
}
