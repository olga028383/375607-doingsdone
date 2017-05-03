<?php
/**
 * Шаблон принимает массив параметров,
 * array $templateData['errors'] содержит массив ошибок для каждого поля,
 * array $templateData['valid'] содержит массив заполненных полей
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <link rel="stylesheet" href="../css/normalize.css">
  <link rel="stylesheet" href="../css/style.css">
</head>

<body class="body-background"><!--class="overlay"-->
  <h1 class="visually-hidden">Дела в порядке</h1>

  <div class="page-wrapper">
    <div class="container">
      <header class="main-header">
        <a href="#">
          <img src="../img/logo.png" width="153" height="42" alt="Логитип Дела в порядке">
        </a>

        <div class="main-header__side">
          <a class="main-header__side-item button button--transparent" href="#">Войти</a>
        </div>
      </header>

      <div class="content">
        <section class="welcome">
          <h2 class="welcome__heading">«Дела в порядке»</h2>

          <div class="welcome__text">
            <p>«Дела в порядке» — это веб приложение для удобного ведения списка дел. Сервис помогает пользователям не забывать о предстоящих важных событиях и задачах.</p>

            <p>После создания аккаунта, пользователь может начать вносить свои дела, деля их по проектам и указывая сроки.</p>
          </div>

          <a class="welcome__button button" href="#">Зарегистрироваться</a>
        </section>
      </div>
    </div>
  </div>
  <div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Вход на сайт</h2>

    <form class="form" class="" action="index.php" method="post">
      <div class="form__row">
        <label class="form__label" for="email">E-mail <sup>*</sup></label>

        <input class="form__input <?= setClassError($templateData['errors'], 'email'); ?>" 
               type="text" 
               name="email" 
               id="email" 
               value="<?=$templateData['valid']['email'];?>" 
               placeholder="Введите e-mail">
        
        <?=addRequiredSpan($templateData['errors'], 'email', 'E-mail введён некорректно');?>
        
      </div>

      <div class="form__row">
        <label class="form__label" for="password">Пароль <sup>*</sup></label>

        <input class="form__input <?= setClassError($templateData['errors'], 'password'); ?>"
               type="password" name="password" 
               id="password"
               value="<?=$templateData['valid']['password'];?>" 
               placeholder="Введите пароль">
        
        <?addRequiredSpan($templateData['errors'], 'password', 'Пароль введён некорректно');?>
      </div>

      <div class="form__row">
        <label class="checkbox">
          <input class="checkbox__input visually-hidden" type="checkbox" checked>
          <span class="checkbox__text">Запомнить меня</span>
        </label>
      </div>

      <div class="form__row form__row--controls">
        <input class="button" type="submit" name="sendAuth" value="Войти">
      </div>
    </form>
  </div>
</body>
</html>
