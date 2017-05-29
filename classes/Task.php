<?php

/**
 * Class Task
 */
class Task {

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
        $sqlGetTasks = "SELECT name as task, deadline, project_id as project, id, file, complete FROM tasks WHERE user_id = ?";
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
     * Функция получает задачи, id проектов, файл, id и метки для задач (выполнена или нет) по поиску
     * @param array $user даные авторизованного пользователя
     * @param string $query запрос поиска
     * @return array массив задач и проектов, соответствующих авторизованному пользователю
     */
    public static function getTasksByProjectOnRequestFilter($user, $query)
    {
        $param = '';
        $condition = '';
        switch ($query) {
            case 'today' :
                $param = "AND deadline LIKE ?";
                $time = date('Y-m-d', time());
                $condition = "%$time%";
                break;
            case 'tomorrow':
                $param = "AND deadline LIKE ?";
                $time = date('Y-m-d', strtotime("tomorrow"));
                $condition = "%$time%";
                break;
            case 'overdue':
                $param = "AND deadline < ?";
                $time = date('Y-m-d H:i:s', strtotime('00:00:00'));
                $condition = "$time";
                break;
        }
        $sqlGetTasks = "SELECT name as task, deadline, project_id as project, id, file, complete FROM tasks WHERE user_id = ? " . $param;
        return Database::instance()->getData($sqlGetTasks, [$user['id'], $condition]);
    }

    /**
     * Функция получает задачи, id проектов, файл, id и метки для задач (выполнена или нет) по поиску
     * @param  array $user даные авторизованного пользователя
     * @param string $query запрос поиска
     * @return array массив задач и проектов, соответствующих авторизованному пользователю
     */
    public static function getTasksByProjectOnRequest($user, $query)
    {
        $sqlGetTasks = "SELECT name as task, deadline, project_id as project, id, file, complete FROM tasks WHERE user_id = ? AND `name` LIKE ?";
        return Database::instance()->getData($sqlGetTasks, [$user['id'], "%$query%"]);
    }

    /**
     * Функция добавляет задачу в базу
     * @param  array $resultAddTask валидные и не валидные поля
     * @param  string $pathFile путь к файлу либо null
     * @param  array $user данные авторизованного пользователя
     */
    public static function addTaskToDatabase($resultAddTask, $pathFile, $user)
    {
        $file = ($pathFile) ? '/upload/' . $pathFile : '';
        $user_id = $user['id'];
        $project_id = (int)$resultAddTask['project'];
        $deadline = checkForDateCorrected($resultAddTask['deadline']);
        $name = $resultAddTask['task'];
        $sqlAddTask = "INSERT INTO tasks(user_id, project_id, created, deadline, name, file) VALUES ( ?, ?, NOW(), ?, ?, ?)";
        Database::instance()->setData($sqlAddTask, [$user_id, $project_id, $deadline, $name, $file]);
    }

    /**
     * Функция копирует задачу
     * @param  array $task задача для копирования
     */
    public static function duplicateTaskToDatabase($task)
    {
        $sqlAddTask = "INSERT INTO tasks(user_id, project_id, created, deadline, name, file) VALUES ( ?, ?, NOW(), ?, ?, ?)";
        Database::instance()->setData($sqlAddTask, [$task['user_id'], $task['project_id'], $task['deadline'], $task['name'], $task['file']]);
    }
}
