<?php
require_once 'init.php';
// Проверка что id вообще передан
if (!isset($_GET['id'])) {
    header("HTTP/1.0 404 Not Found");
    exit;
}
session_start();
// Проверка что таск с таким id вообще есть и он наш (проверяется внутри getTaskById)
$id = $_GET['id'];
$user = Auth::getAuthUser();;
$task = Task::getTaskById($id, $user);
if (empty($task)) {
    header("HTTP/1.0 404 Not Found");
    exit;
}
// Копируем таск, редиректим в начало
Task::duplicateTaskToDatabase($task);
header("Location: /index.php");