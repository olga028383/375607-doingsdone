<?
session_start();
error_reporting(E_ALL);

require_once 'init.php';
/**
 * Функция проверяет форму и добавляет категорию
 * @param $taskForm объект задачи
 * @param $user
 */
function showAddTaskFormIfNeeded($taskForm, $user = [])
{
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
        Task::addTaskToDatabase($taskForm->getFormData(), $path, $user);
        header("Location: /index.php");
        exit();
    }
}

/**
 * Функция проверяет форму и добавляет категорию
 * @param $categoryForm объект формы
 * @param $user
 */
function showAddProjectFormIfNeeded($categoryForm, $user = [])
{
    $categoryForm->validate();
    if ($categoryForm->isValid()) {
        /* Функция добавляет категорию в базу */
        Project::addProject($categoryForm->getformData(), $user);
        header("Location: /index.php");
        exit();
    }

}

/* Инициализация переменных */
$bodyClassOverlay = '';
$modalShowTask = false;
$modalShowProject = false;
$taskList = [];

//Записываю сессию с информацией о пльзователе в переменную, если она есть
$user = Auth::getAuthUser();
if ($user) {
    //Если пользователь залогинен, загрузим задачи, проекты в зависимости от переданных параметров
    list($filter, $search, $projectList, $taskList) = getTasksAndProjects($user);
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
// Валидация формы добавления задач для проекта
$taskForm = new AddTaskForm();
if (isset($_GET['addTask']) || $taskForm->isSubmitted()) {
    $bodyClassOverlay = 'overlay';
    $modalShowTask = true;
}
if ($taskForm->isSubmitted()) {
    showAddTaskFormIfNeeded($taskForm, $user);
}

// Валидация формы добавления категорий для проекта
$categoryForm = new AddCategoryForm();
if (isset($_GET['addProject']) || $categoryForm->isSubmitted()) {
    $bodyClassOverlay = 'overlay';
    $modalShowProject = true;
}
if ($categoryForm->isSubmitted()) {
    showAddProjectFormIfNeeded($categoryForm, $user);
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
    print (includeTemplate('main.php', [
            'projects' => $projectList,
            'tasksToDisplay' => $tasksToDisplay,
            'allTasks' => $taskList,
            'show_completed' => $show_completed,
            'filter' => $filter,
            'search' => $search
        ]
    ));
}
printEndDivLayout();

print includeTemplate('footer.php', ['user' => $user]);
if ($modalShowTask) {
    print(includeTemplate('add-task.php', ['form' => $taskForm, 'projects' => $projectList]));
}
if ($modalShowProject) {
    print(includeTemplate('add-project.php', ['form' => $categoryForm]));
}
printEndBodyHtml();
?>

