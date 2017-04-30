<?php
ob_start();
error_reporting(E_ALL);
require_once 'functions.php';
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

// Если пришёл get-параметр add, то покажем форму добавления проекта
$bodyClassOverlay = '';
$modalShow = false;
if (isset($_GET['add']) || isset($_POST['send'])) {
    $bodyClassOverlay = 'overlay';
    $modalShow = true;
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
        <?= includeTemplate('header.php', []); ?>

        <?= includeTemplate('main.php', ['projects' => $projectList, 'tasksToDisplay' => $tasksToDisplay, 'allTasks' => $taskList]); ?>
      </div>
    </div>
    <?= includeTemplate('footer.php', []); ?>

    <?php
    if ($modalShow) {
        print(includeTemplate('add-project.php', ['errors' => $errors, 'projects' => $projectList, 'newTask' => $newTask]));
    }
    ?>
    <script type="text/javascript" src="js/script.js"></script>
  </body>
</html>
