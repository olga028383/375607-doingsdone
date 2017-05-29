<?php
error_reporting(E_ALL);
require_once 'init.php';


$timeFrom = date('Y-m-d H:i:s');
$timeTo = date('Y-m-d H:i:s', time() + 3600);
$sql = "SELECT tasks.id, tasks.name, tasks.deadline, user.email, user.name as user
        FROM tasks
        LEFT JOIN notifications
        ON tasks.id = notifications.task_id
        LEFT JOIN user
        ON tasks.user_id = user.id
        WHERE notifications.id IS NULL
        AND tasks.complete IS NULL
        AND tasks.deadline >= ?
        AND tasks.deadline <=";
$result = Database::instance()->getData($sql, [$timeFrom, $timeTo]);

// Составляем массив пользователей и сообщений и делаем отметку в таблицу notifications
$arr = [];
foreach ($result as $value) {
    if (array_key_exists($value['email'], $arr)) {
        $arr[$value['email']]['text'] .= '<li>Задача ' . $value['name'] . ' на ' . $value['deadline'] . '</li>';
    } else {
        $arr[$value['email']]['text'] = '<li>Задача ' . $value['name'] . ' на ' . $value['deadline'] . '</li>';
        $arr[$value['email']]['name'] = $value['user'];
    }
    $sqlAddNotification = "INSERT INTO notifications(task_id, sent_on) VALUES ( ?, NOW())";
    Database::instance()->setData($sqlAddNotification, [$value['id']]);
}

// Рассылаем письма
$count = 0;
foreach ($arr as $key => $value) {
    sendMail($key, message($value['name'], $value['text']));
    ++$count;
}
echo "Выслали $count писем\n";

/**
 * Функция составляет сообщение
 * @param string $name имя пользователя
 * @param string $task задачи
 * @return string
 */
function message($name, $task)
{
    return "<h1>Уважаемый пользователь, $name</h1><p>У Вас запланировано </p><ul>$task</ul>";
}

/**
 * Функция записывает в файл рассылаемые сообщения
 * @param string $email
 * @param string $message
 */
function sendMail($email, $message)
{
    $file = 'mail.html';
    $handler = fopen($file, "a");
    $text = "Пользователь : $email . Сообщение: $message";
    fwrite($handler, iconv("UTF-8", "UTF-8", "$text"));
    fclose($handler);
    echo "Отправлено письмо пользователю ".$email."\n";
}
