<?
session_start();
error_reporting(E_ALL);

require_once 'init.php';
/* Инициализация переменных */
$user = [];
$bodyClassOverlay = '';
$modalShow = false;
$modalShowCategory = false;
$showAuthenticationForm = false;
$showPageRegister = false;
$messageAfterRegistered = false;
$taskList = [];
//Соединение с б/д
$dbConnection = Database::instance();
//проверяем, если пришел параметр register, то подключаем шаблон формы регистрации
if (isset($_GET['register'])) {
    $showPageRegister = true;
}
// Если пришёл get-параметр login или sendAuth, то покажем форму регистрации
if (isset($_GET['login']) || isset($_POST['sendAuth'])) {
    $bodyClassOverlay = 'overlay';
    $showAuthenticationForm = true;
}
//Если пользователь только что авторизовался, то покажем ему сообщение
if (isset($_GET['login']) && isset($_GET['show_message'])) {
    $messageAfterRegistered = true;
}

//Валидация формы для авторизации пользователя
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
    $project = (int)abs(($_GET['project']));
    $valID = null;
    foreach ($projectList as $value) {
        if ($value['id'] !== $project) {
            continue;
        } else {
            $valID = $value['id'];
        }
    }
    if ($valID || $project == 0) {
        $tasksToDisplay = array_filter($taskList, function ($task) use ($valID, $project) {
            return $project == 0 || $valID == $task['project'];
        });
    } else {
        header("HTTP/1.0 404 Not Found");
        exit();
    }
} else {
    $tasksToDisplay = $taskList;
}

//Валидация формы добавления задачи для проекта
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
        $path = null;
        if (isset($_FILES['preview'])) {
            $file = $_FILES['preview'];
            if (is_uploaded_file($file['tmp_name'])) {
                move_uploaded_file($file['tmp_name'], __DIR__ . '/upload/' . $file['name']);
            }
            $path = $file['name'];
        }
        /* Функция добавляет задачу в базу */
        addTaskToDatabase($dbConnection, $resultAddTask, $path, $user);
        $bodyClassOverlay = '';
        $modalShow = false;
        header("Location: /index.php");
        exit();
    }
    $dataFormAddTask = $resultAddTask;
}

// Валидация формы добавления категорий для проекта
$dataFormAddCategory = AddkeysForValidation(['task']);
if (isset($_GET['addCategory']) || isset($_POST['sendCategory'])) {
    $bodyClassOverlay = 'overlay';
    $modalShowCategory = true;
}
if (isset($_POST['sendCategory'])) {
    $resultAddCategory = validateTaskForm(['task']);
    if (!$resultAddCategory['error']) {
        /* Функция добавляет категорию в базу */
        addCategoryToDatabase($dbConnection, $resultAddCategory, $user);
        $bodyClassOverlay = '';
        $modalShowCategory = false;
    }
    $dataFormAddCategory = $resultAddCategory;
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
<?= printHead(); ?>

<body class=<?= $bodyClassOverlay; ?>>
<h1 class="visually-hidden">Дела в порядке</h1>

<div class="page-wrapper">
    <div class="container container--with-sidebar">
        <?= includeTemplate('header.php', ['user' => $user]); ?>
        <?php
        if (!$user) {
            print(includeTemplate('guest.php', []/*$dataForHeaderTemplate + ['showAuthenticationForm' => $showAuthenticationForm] + ['messageAfterRegistered' => $messageAfterRegistered]*/));
        } else {
            print (includeTemplate('main.php', ['projects' => $projectList, 'tasksToDisplay' => $tasksToDisplay, 'allTasks' => $taskList, 'show_completed' => $show_completed]));
        }
        ?>
    </div>
</div>
<?php
print includeTemplate('footer.php', ['user' => $user]);
if ($modalShow) {
    print(includeTemplate('add-project.php', $dataFormAddTask + ['projects' => $projectList]));
}
if ($modalShowCategory) {
    print(includeTemplate('add-category.php', $dataFormAddCategory));
}
printEndBodyHtml();
?>

