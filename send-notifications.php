<?php
error_reporting(E_ALL);
require_once 'init.php';
$time = date('Y-m-d H:i:s', time() - 3600);
$sql = "SELECT tasks.name, tasks.deadline, user.email, user.name as user
        FROM tasks
        LEFT OUTER JOIN notifications
        ON tasks.id = notifications.task_id 
        LEFT JOIN user
        ON tasks.user_id = user.id
        WHERE notifications.id IS NULL
        AND tasks.complete IS NULL
        AND tasks.deadline >= ?";
$result = Database::instance()->getData($sql, [$time]);
/*составляем массив пользователей и сообщений*/
$arr = array();
foreach ($result as $value) {
    if (array_key_exists($value['email'], $arr)) {
        $arr[$value['email']]['text'] .= '<li>Задача ' . $value['name'] . ' на ' . $value['deadline'] . '</li>';
    } else {
        $arr[$value['email']]['text'] = '<li>Задача ' . $value['name'] . ' на ' . $value['deadline'] . '</li>';
        $arr[$value['email']]['name'] = $value['user'];
    }
}
/*рассылаем письма*/
foreach ($arr as $key => $value) {
    mail($key, 'Уведомление от сервиса «Дела в порядке»', message($value['name'], $value['text']));
}
/**
 * Функция составляет сообщение
 * @return string
 */
function message($name, $text)
{
    return "<h1>Уважаемый пользователь, $name</h1><p>У Вас запланировано </p><ul>$text</ul>";
}
