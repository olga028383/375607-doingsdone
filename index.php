<?
session_start();
error_reporting(E_ALL);

require_once 'init.php';

/**
 * Показ формы добавления задачи и обработка поста в нее
 * @param $user
 * @return AddTaskForm|null
 */
function showAddTaskFormIfNeeded($user = [])
{
    $taskForm = new AddTaskForm();
    if (isset($_GET['addTask']) && !$taskForm->isSubmitted()) {
        // Просто показ формы
        return $taskForm;
    }

    if ($taskForm->isSubmitted()) {
        // POST формы
        $taskForm->validate();
        if ($taskForm->isValid()) {
            $file = null;
            $fileName = null;
            if (isset($_FILES['preview'])) {
                $file = $_FILES['preview'];
                if (is_uploaded_file($file['tmp_name'])) {
                    move_uploaded_file($file['tmp_name'], __DIR__ . '/upload/' . $file['name']);
                }
                $fileName = $file['name'];
            }
            /* Функция добавляет задачу в базу */
            Task::addTaskToDatabase($taskForm->getFormData(), $fileName, $user);
            header("Location: /index.php");
            exit();
        } else {
            return $taskForm;
        }
    }

    return null;
}

/**
 * Показ формы добавления проекта и обработка поста в нее
 * @param $user
 * @return AddCategoryForm|null
 */
function showAddProjectFormIfNeeded($user = [])
{
    $categoryForm = new AddCategoryForm();
    if (isset($_GET['addProject']) && !$categoryForm->isSubmitted()) {
        // Просто показ формы
        return $categoryForm;
    }

    if ($categoryForm->isSubmitted()) {
        // POST формы
        $categoryForm->validate();
        if ($categoryForm->isValid()) {
            /* Функция добавляет категорию в базу */
            Project::addProject($categoryForm->getformData(), $user);
            header("Location: /index.php");
            exit();
        } else {
            return $categoryForm;
        }
    }

    return null;
}

/**
 * Функция возвращает список проектов и задач
 * @param array $user принимает массив с данными авторизованного пользователя
 * @return array
 */
function getTasksAndProjects($user)
{
    if (!$user) {
        return ['', '', [], [], []];
    }

    $filter = false;
    $search = false;
    /* Получаем  задачи и проекты после того как пользователь авторизован */
    $projectList = Project::getProjects($user);
    /* Получаем массив задач из базы в зависимости от запроса*/
    if (isset($_GET['search']) && !empty(sanitizeInput($_GET['search']))) {
        $query = trim($_GET['search']);
        $taskList = Task::getTasksByProjectOnRequest($user, $query);
        $search = trim($_GET['search']);
    } else if (isset($_GET['filter'])) {
        $filter = sanitizeInput($_GET['filter']);
        $taskList = Task::getTasksByProjectOnRequestFilter($user, $filter);
        $filter = sanitizeInput($_GET['filter']);
    } else {
        $taskList = Task::getTasksByProject($user);
    }
    // Если пришел get-параметр project, то отфильтруем все таски про проекту
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

    return [$filter, $search, $projectList, $taskList, $tasksToDisplay];
}


// Записываю сессию с информацией о пльзователе в переменную, если она есть
$user = Auth::getAuthUser();
list($filter, $search, $projectList, $taskList, $tasksToDisplay) = getTasksAndProjects($user);

// Если пришел параметр show_completed создаем куку
$showCompleted = false;
if (isset($_GET['show_completed'])) {
    $showCompleted = sanitizeInput($_GET['show_completed']);
    setcookie('show_completed', $showCompleted, strtotime("+30 days"));
} else if (isset($_COOKIE['show_completed'])) {
    $showCompleted = $_COOKIE['show_completed'];
}

// Отрисовка страницы

$taskForm = showAddTaskFormIfNeeded($user);
$categoryForm = showAddProjectFormIfNeeded($user);

printHead($taskForm !== null || $categoryForm !== null);

print includeTemplate('header.php', ['user' => $user]);

if (!$user) {
    print includeTemplate('guest.php', []);
} else {
    print includeTemplate('main.php', [
            'projects' => $projectList,
            'tasksToDisplay' => $tasksToDisplay,
            'allTasks' => $taskList,
            'show_completed' => $showCompleted,
            'filter' => $filter,
            'search' => $search
        ]
    );
}
printEndDivLayout();

print includeTemplate('footer.php', ['user' => $user]);

if ($taskForm) {
    print includeTemplate('add-task.php', ['form' => $taskForm, 'projects' => $projectList]);
}
if ($categoryForm) {
    print includeTemplate('add-project.php', ['form' => $categoryForm]);
}
printEndBodyHtml();

?>