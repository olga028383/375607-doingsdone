<?
session_start();
error_reporting(E_ALL);

require_once 'init.php';
/* Инициализация переменных */
$bodyClassOverlay = '';
$modalShow = false;
$modalShowCategory = false;
$showPageRegister = false;
$taskList = [];
//проверяем, если пришел параметр register, то подключаем шаблон формы регистрации
if (isset($_GET['register'])) {
    $showPageRegister = true;
}
//Записываю сессию с информацией о пльзователе в переменную, если она есть
$user = Auth::logout();
/* Получаем  задачи и проекты после того как пользователь авторизован */
if (is_object(Database::instance()->resultConnection()) && $user) {
    /* Получаем массив проектов из базы данных, и добавляет 1 значение "Все" */
    $projectList = getProjects($user);
    /* Получаем массив задач из базы */
    if (isset($_GET['search']) && !empty(sanitizeInput($_GET['search']))) {
        $query = trim($_GET['search']);
        $taskList = getTasksByProjectOnRequest($user, $query);
    } else if (isset($_GET['filter'])) {
        $filter = sanitizeInput($_GET['filter']);
        $taskList = getTasksByProjectOnRequestFilter($user, $filter);
    } else {
        $taskList = getTasksByProject($user);
    }
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
$taskForm = new AddTaskForm();
// Если пришёл get-параметр add, то покажем форму добавления проекта
if (isset($_GET['add']) || $taskForm->isSubmitted()) {
    $bodyClassOverlay = 'overlay';
    $modalShow = true;
}
if ($taskForm->isSubmitted()) {
    $taskForm->validate();
    if ($taskForm->isValid()) {
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
        addTaskToDatabase($taskForm->getformData(), $path, $user);
        $bodyClassOverlay = '';
        $modalShow = false;
        header("Location: /index.php");
        exit();
    }
}

// Валидация формы добавления категорий для проекта
$categotyForm = new AddCategoryForm();
if (isset($_GET['addCategory']) || $categotyForm->isSubmitted()) {
    $bodyClassOverlay = 'overlay';
    $modalShowCategory = true;
}
if ($categotyForm->isSubmitted()) {
    $categotyForm->validate();
    if ($categotyForm->isValid()) {
        /* Функция добавляет категорию в базу */
        addCategoryToDatabase($categoryForm->getformData(), $user);
        $bodyClassOverlay = '';
        $modalShowCategory = false;
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
printHead($bodyClassOverlay);

print(includeTemplate('header.php', ['user' => $user]));
if (!$user) {
    print(includeTemplate('guest.php', []));
} else {
    print (includeTemplate('main.php', ['projects' => $projectList, 'tasksToDisplay' => $tasksToDisplay, 'allTasks' => $taskList, 'show_completed' => $show_completed]));
}
printEndDivLayout();

print includeTemplate('footer.php', ['user' => $user]);
if ($modalShow) {
    print(includeTemplate('add-project.php', ['projects' => $projectList, 'form' => $taskForm]));
}
if ($modalShowCategory) {
    print(includeTemplate('add-category.php', ['form' => $categotyForm]));
}
printEndBodyHtml();
?>

