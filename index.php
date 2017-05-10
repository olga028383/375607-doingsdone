<?
session_start();
error_reporting(E_ALL);
require_once 'mysql_helper.php';
require_once 'functions.php';
require_once 'userdata.php';
/* Соединение с б/д */
$dbConnection = setConnection('localhost', 'mysql', 'mysql', 'thingsarefine');
if (is_object($dbConnection)) {
    /* Выполнить запрос и получить данные */
    $sql = "SELECT name FROM `projects` WHERE user_id = ?";
    $projects = getData($dbConnection, $sql, [1]);
    /* Выполнить запрос и добавить данные */
    $sql = "INSERT INTO tasks(user_id, project_id, created, deadline, name) VALUES (?, ?, CURDATE(), CURDATE()+ INTERVAL 1 DAY, ?)";
    $id = setData($dbConnection, $sql, [3, 4, 'Помыть посуду']);
    /* Выполнить запрос и обновить данные */
    $numValue = updateData($dbConnection, 'tasks', ['name' => 'Еще чего-н сделать'], ['id' => 7]);
}

$projectList = ["Все", "Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];
$taskList = [
    [
        "task" => "Собеседование в IT компании",
        "date" => "01.06.2017",
        "project" => "Работа",
        "result" => "Нет"
    ],
    [
        "task" => "Выполнить тестовое задание",
        "date" => "25.05.2017",
        "project" => "Работа",
        "result" => "Нет"
    ],
    [
        "task" => "Сделать задание первого раздела",
        "date" => "21.04.2017",
        "project" => "Учеба",
        "result" => "Да"
    ],
    [
        "task" => "Встреча с другом",
        "date" => "22.04.2017",
        "project" => "Входящие",
        "result" => "Нет"
    ],
    [
        "task" => "Купить корм для кота",
        "date" => "Нет",
        "project" => "Домашние дела",
        "result" => "Нет"
    ],
    [
        "task" => "Заказать пиццу",
        "date" => "Нет",
        "project" => "Домашние дела",
        "result" => "Нет"
    ]
];

$user = [];
$bodyClassOverlay = '';
$modalShow = false;
$showAuthenticationForm = false;
// Если пришёл get-параметр login или sendAuth, то покажем форму регистрации
if (isset($_GET['login']) || isset($_POST['sendAuth'])) {
    $bodyClassOverlay = 'overlay';
    $showAuthenticationForm = true;
}

$dataForHeaderTemplate = AddkeysForValidation(['email', 'password']);

if (isset($_POST['sendAuth'])) {

    $resultAuth = validateLoginForm($users);

    if (!$resultAuth['error']) {
        if (password_verify($_POST['password'], $resultAuth['user']['password'])) {
            $_SESSION['user'] = $resultAuth['user'];
            header("Location: /index.php");
            exit();
        } else {
            $resultAuth['output']['errors']['password'] = true;
        }
    }
    $dataForHeaderTemplate = $resultAuth['output'];
}

//Записываю сессию с информацией о пльзователе в переменную, если она есть
$user = (isset($_SESSION['user'])) ? $_SESSION['user'] : [];

// Если пришёл get-параметр add, то покажем форму добавления проекта
if (isset($_GET['add']) || isset($_POST['send'])) {
    $bodyClassOverlay = 'overlay';
    $modalShow = true;
}
// Если пришел get-параметр project, то отфильтруем все таски про проекту
$tasksToDisplay = [];
$project = '';
if (isset($_GET['project'])) {
    $project = (int) abs(($_GET['project']));

    if ($project > count($taskList) - 1) {
        header("HTTP/1.0 404 Not Found");
        exit();
    } else {
        $tasksToDisplay = array_filter($taskList, function($task) use ($projectList, $project) {
            return $project == 0 || $projectList[$project] == $task['project'];
        });
    }
} else {
    $tasksToDisplay = $taskList;
}

$expectedFields = ['task', 'project', 'date'];
// Инициализируем все ожидаемые поля в $newTask, и сбрасываем $errors в false для каждого поля
$newTask = ['result' => 'Нет'];
$errors = [];
foreach ($expectedFields as $field) {
    $newTask[$field] = '';
    $errors[$field] = false;
}
if (isset($_POST['send'])) {
    $errorsFound = false;
    foreach ($expectedFields as $name) {
        if (!empty($_POST[$name])) {
            $newTask[$name] = sanitizeInput($_POST[$name]);
        } else {
            $errors[$name] = true;
            $errorsFound = true;
        }
    }
    if (!$errorsFound) {
        array_unshift($tasksToDisplay, $newTask);
        $bodyClassOverlay = '';
        $modalShow = false;
    }
    if (isset($_FILES['preview'])) {
        $file = $_FILES['preview'];
        if (is_uploaded_file($file['tmp_name'])) {
            move_uploaded_file($file['tmp_name'], __DIR__ . '/upload/' . $file['name']);
        }
    }
}
//если пришел параметр show_completed создаем куку
$show_completed = false;
if (isset($_GET['show_completed'])) {
    $show_completed = sanitizeInput($_GET['show_completed']);
    setcookie('show_completed', $show_completed, strtotime("+30 days"));
} else if (isset($_COOKIE['show_completed'])) {
    $show_completed = $_COOKIE['show_completed'];
}
?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <title>Дела в Порядке!</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body class=<?= $bodyClassOverlay; ?>>
    <h1 class="visually-hidden">Дела в порядке</h1>

    <div class="page-wrapper">
      <div class="container container--with-sidebar">
        <?= includeTemplate('header.php', ['user' => $user]); ?>
        <?php
        if (!$user) {
            print(includeTemplate('guest.php', $dataForHeaderTemplate + ['showAuthenticationForm' => $showAuthenticationForm]));
        } else {
            print (includeTemplate('main.php', ['projects' => $projectList, 'tasksToDisplay' => $tasksToDisplay, 'allTasks' => $taskList, 'show_completed' => $show_completed]));
        }
        ?>
      </div>
    </div>
    <?php
    print includeTemplate('footer.php', ['user' => $user]);
    if ($modalShow) {
        print(includeTemplate('add-project.php', ['errors' => $errors, 'projects' => $projectList, 'newTask' => $newTask]));
    }
    ?>
    <script type="text/javascript" src="js/script.js"></script>
  </body>
</html>
