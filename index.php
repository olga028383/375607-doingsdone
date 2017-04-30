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

$bodyClassOverlay = '';
$modalShow = false;
if (isset($_GET['add'])) {
    $bodyClassOverlay = 'overlay';
    $modalShow = true;
}
$errors = ['task' => false, 'project' => false, 'date' => false];
$newTask = ['result' => 'Нет', 'task' => '', 'project' => '', 'date' => ''];
if (isset($_POST['send'])) {
    $expectedFields = ['task', 'project', 'date'];
    foreach ($expectedFields as $name) {
        if (!empty($_POST[$name])) {
            $newTask[$name] = checkInput($_POST[$name]);
        } else {
            $errors[$name] = true;
            $bodyClassOverlay = 'overlay';
            $modalShow = true;
        }
    }
    if (count($newTask) > 3) {
        array_unshift($tasksToDisplay, $newTask);
    }
    if (isset($_FILES['preview'])) {
        $file = $_FILES['preview'];
        var_dump($file);
        move_uploaded_file($file['tmp_name'], '/');
    }
    //if (is_uploaded_file($_FILES['preview']['tmp_name'])){
    //  $file = $_FILES['preview'];
    //  move_uploaded_file($file['tmp_name'], '/');
    //}
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
        print(includeTemplate('add-project.php', ['errors' => $errors, 'allTasks' => $projectList, 'newTask' => $newTask]));
    }
    ?>
    <script type="text/javascript" src="js/script.js"></script>
  </body>
</html>
