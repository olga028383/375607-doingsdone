<?php
error_reporting(E_ALL);
require_once 'functions.php';
$projectList = ["Все", "Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];
$taskList = [
    [
        "task" => "Собеседование в IT компании",
        "date" => "01.06.2017",
        "category" => "Работа",
        "result" => "Нет"
    ],
    [
        "task" => "Выполнить тестовое задание",
        "date" => "25.05.2017",
        "category" => "Работа",
        "result" => "Нет"
    ],
    [
        "task" => "Сделать задание первого раздела",
        "date" => "21.04.2017",
        "category" => "Учеба",
        "result" => "Да"
    ],
    [
        "task" => "Встреча с другом",
        "date" => "22.04.2017",
        "category" => "Входящие",
        "result" => "Нет"
    ],
    [
        "task" => "Купить корм для кота",
        "date" => "Нет",
        "category" => "Домашние дела",
        "result" => "Нет"
    ],
    [
        "task" => "Заказать пиццу",
        "date" => "Нет",
        "category" => "Домашние дела",
        "result" => "Нет"
    ]
];

function getNumberTasks($taskList, $nameCategory) {
    if (!$nameCategory) {
        return 0;
    }
    if ($nameCategory == "Все") {
        return count($taskList);
    }

    $countTask = 0;
    foreach ($taskList as $key => $value) {
        if ($value["category"] == $nameCategory) {
            $countTask ++;
        }
    }
    return $countTask;
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

  <body><!--class="overlay"-->
    <h1 class="visually-hidden">Дела в порядке</h1>

    <div class="page-wrapper">
      <div class="container container--with-sidebar">
        <?= includeTemplate('header.php', []); ?>

        <?= includeTemplate('main.php', [$projectList, $taskList]); ?>

        <?= includeTemplate('footer.php', []); ?>
        <div class="modal" hidden>
          <button class="modal__close" type="button" name="button">Закрыть</button>

          <h2 class="modal__heading">Добавление задачи</h2>

          <form class="form" class="" action="index.html" method="post">
            <div class="form__row">
              <label class="form__label" for="name">Название <sup>*</sup></label>

              <input class="form__input" type="text" name="name" id="name" value="" placeholder="Введите название">
            </div>

            <div class="form__row">
              <label class="form__label" for="project">Проект <sup>*</sup></label>

              <select class="form__input form__input--select" name="project" id="project">
                <option value="">Входящие</option>
              </select>
            </div>

            <div class="form__row">
              <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>

              <input class="form__input form__input--date" type="text" name="date" id="date" value="" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
            </div>

            <div class="form__row">
              <label class="form__label" for="file">Файл</label>

              <div class="form__input-file">
                <input class="visually-hidden" type="file" name="preview" id="preview" value="">

                <label class="button button--transparent" for="preview">
                  <span>Выберите файл</span>
                </label>
              </div>
            </div>

            <div class="form__row form__row--controls">
              <input class="button" type="submit" name="" value="Добавить">
            </div>
          </form>
        </div>

        <script type="text/javascript" src="js/script.js"></script>
        </body>
        </html>
