<?
session_start();
error_reporting(E_ALL);
require_once 'mysql_helper.php';
require_once 'functions.php';

/* Инициализация переменных */
$user = [];
$bodyClassOverlay = '';
$modalShow = false;
$showAuthenticationForm = false;
$showPageRegister = false;
$messageAfterRegistered = false;
$taskList = [];
//Соединение с б/д
$dbConnection = setConnection('localhost', 'mysql', 'mysql', 'thingsarefine');
//проверяем, если пришел параметр register, то подключаем шаблон формы регистрации
if (isset($_GET['register']) || isset($_POST['register'])) {
    $showPageRegister = true;
}
$dataForRegisterTemplate = AddkeysForValidation(['email', 'name', 'password']);
if (isset($_POST['register'])) {

    $resultRegister = validateLoginForm($dbConnection, ['email', 'name', 'password']);
    if (!$resultRegister['error']) {
        /* Функция добавляет пользователя в базу */
        addUserToDatabase($dbConnection, $resultRegister);
        header("Location: /index.php?login=message");
        exit();
    }
    if ($resultRegister['user']) {
        $resultRegister['output']['user'] = $resultRegister['user'];
        $dataForRegisterTemplate = $resultRegister['output'];
    } else {
        $resultRegister['output']['user'] = null;
        $dataForRegisterTemplate = $resultRegister['output'];
    }
}

// Если пришёл get-параметр login или sendAuth, то покажем форму регистрации
if (isset($_GET['login']) || isset($_POST['sendAuth'])) {
    /* if($_GET['login'] == 'message'){
      $messageAfterRegistered = true;
      } */
    $bodyClassOverlay = 'overlay';
    $showAuthenticationForm = true;
}
$dataForHeaderTemplate = AddkeysForValidation(['email', 'password']);
if (isset($_POST['sendAuth'])) {

    $resultAuth = validateLoginForm($dbConnection, ['email', 'password']);

    if (!$resultAuth['error']) {
        if (password_verify($_POST['password'], $resultAuth['user'][0]['password'])) {
            $_SESSION['user'] = $resultAuth['user'][0];
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

/* Получаем  задачи и проекты после того как пользователь авторизован */
if (is_object($dbConnection) && $user) {
    /* Получаем массив проектов из базы данных, и добавляет 1 значение "Все" */
    $projectList = getProjects($dbConnection, $user);
    /* Получаем массив задач из базы */
    $taskList = getTasksByProject($dbConnection, $user);
}

// Если пришел get-параметр project, то отфильтруем все таски про проекту
$tasksToDisplay = [];
$project = '';
if (isset($_GET['project'])) {
    $project = (int) abs(($_GET['project']));
    $valID = null;
    foreach ($projectList as $value) {
        if ($value['id'] !== $project) {
            continue;
        } else {
            $valID = $value['id'];
        }
    }
    if ($valID) {
        $tasksToDisplay = array_filter($taskList, function($task) use ($valID, $project) {
            return $project == 0 || $valID == $task['project'];
        });
    } else {
        header("HTTP/1.0 404 Not Found");
        exit();
    }
} else {
    $tasksToDisplay = $taskList;
}
$dataFormAddTask = AddkeysForValidation(['task', 'project', 'deadline']);
// Если пришёл get-параметр add, то покажем форму добавления проекта
if (isset($_GET['add']) || isset($_POST['send'])) {
    $bodyClassOverlay = 'overlay';
    $modalShow = true;
}
if (isset($_POST['send'])) {
    $resultAddTask = validateTaskForm(['task', 'project', 'deadline']);
    if (!$resultAddTask['error']) {

        $file = null;
        if (isset($_FILES['preview'])) {
            $file = $_FILES['preview'];
            if (is_uploaded_file($file['tmp_name'])) {
                move_uploaded_file($file['tmp_name'], __DIR__ . '/upload/' . $file['name']);
            }
        }

        /* Функция добавляет задачу в базу */
        addTaskToDatabase($dbConnection, $resultAddTask, $file);
        $bodyClassOverlay = '';
        $modalShow = false;
    }
    $dataFormAddTask = $resultAddTask;
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
        if ($showPageRegister) {
            print(includeTemplate('register.php', $dataForRegisterTemplate));
        } else {
            if (!$user) {
                print(includeTemplate('guest.php', $dataForHeaderTemplate + ['showAuthenticationForm' => $showAuthenticationForm] + ['messageAfterRegistered' => $messageAfterRegistered]));
            } else {
                print (includeTemplate('main.php', ['projects' => $projectList, 'tasksToDisplay' => $tasksToDisplay, 'allTasks' => $taskList, 'show_completed' => $show_completed]));
            }
        }
        ?>
      </div>
    </div>
    <?php
    print includeTemplate('footer.php', ['user' => $user]);
    if ($modalShow) {
        print(includeTemplate('add-project.php', $dataFormAddTask + ['projects' => $projectList]));
    }
    ?>
    <script type="text/javascript" src="js/script.js"></script>
  </body>
</html>
