<?php
/**
 * Шаблон принимает массив параметров,
 * array $templateData['user'] содержит логин и пароль пользователя,
 * Если массив пустой, то показываем ссылку входа, если в нем пришли параметры - то показываем шапку интерфейса
 */
?>
<header class="main-header">
  <?php if (!empty($templateData['user'])) : ?>

      <a href="#">
        <img src="img/logo.png" width="153" height="42" alt="Логитип Дела в порядке">
      </a>

      <div class="main-header__side">
        <a class="main-header__side-item button button--plus" href="index.php?add">Добавить задачу</a>

        <div class="main-header__side-item user-menu">
          <div class="user-menu__image">
            <img src="img/user-pic.jpg" width="40" height="40" alt="Пользователь">
          </div>

          <div class="user-menu__data">
            <p>Константин</p>

            <a href="index.php?exit">Выйти</a>
          </div>
        </div>
      </div>
  <?php else: ?>

      <a href="#">
        <img src="../img/logo.png" width="153" height="42" alt="Логитип Дела в порядке">
      </a>

      <div class="main-header__side">
        <a class="main-header__side-item button button--transparent" href="index.php?login">Войти</a>
      </div>

  <?php endif; ?>
</header>
