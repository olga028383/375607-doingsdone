<?php

require_once 'functions.php';

// Проверка что id вообще передан
if (!isset($_GET['id'])) {
    header("HTTP/1.0 404 Not Found");
    exit;
}
session_start();

// Проверка что таск с таким id вообще есть и он наш (проверяется внутри getTaskById)
$id = $_GET['id'];
$dbConnection = setConnection();
$user = (isset($_SESSION['user'])) ? $_SESSION['user'] : [];
$task = getTaskById($dbConnection, $id, $user);
if (empty($task)) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

// Таск наш, помечаем как выполненный, редиректим в начало
updateData($dbConnection, 'tasks', ['complete = ' => date("Y-m-d H:i:s", time())], ['id =' => (int)$id]);
header("Location: /index.php");
