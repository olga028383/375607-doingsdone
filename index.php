<?php
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
if (isset($_POST['name']) || isset($_POST['project']) || isset($_POST['date'])) {
    $nameError = false;
    $newProjectError = false;
    $dateError = false;
    $name = '';
    $newProject = '';
    $date = '';

    if (empty($_POST['name'])) {
        $nameError = true;
        $bodyClassOverlay = 'overlay';
        $modalShow = true;
    }else{
        $name = checkInput($_POST['name']);
    }
    if (empty($_POST['project'])) {
        $newProjectError = true;
        $bodyClassOverlay = 'overlay';
        $modalShow = true;
    }else{
         $newProject = checkInput($_POST['project']);
    }
    if (empty($_POST['date'])) {
        $dateError = true;
        $bodyClassOverlay = 'overlay';
        $modalShow = true;
    }else{
        $date = checkInput($_POST['date']);
    }
    array_unshift($tasksToDisplay, ["task" => $name, "date" => $date, "project" => $newProject, "result" => "Нет"]);

    /* if (is_uploaded_file($_FILES['preview']['name'])) {
      $file = $_FILES['preview'];
      move_uploaded_file($file['tmp_name'], '/');
      } */
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
        print(includeTemplate('add-project.php', ['nameError' => $nameError, 'newProjectError' => $newProjectError, 'dateError' => $dateError, 'allTasks' => $projectList]));
    }
    ?>
    <script type="text/javascript" src="js/script.js"></script>
  </body>
</html>
