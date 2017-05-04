<?php
/**
 * Шаблон принимает массив параметров,
 * array $templateData['errors'] содержит массив ошибок для каждого поля,
 * array $templateData['valid'] содержит массив заполненных полей
 * bool $templateData['showAuthenticationForm']
 */
$showModalAuth = '';
if (!$templateData['showAuthenticationForm']) {
    $showModalAuth = 'hidden';
}
?>


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

<div class="modal" <?= $showModalAuth; ?> >
  <a href="/" class="modal__close" name="button">Закрыть</a>

  <h2 class="modal__heading">Вход на сайт</h2>

  <form class="form" class="" action="index.php" method="post">
    <div class="form__row">
      <label class="form__label" for="email">E-mail <sup>*</sup></label>

      <input class="form__input <?= setClassError($templateData['errors'], 'email'); ?>" 
             type="text" 
             name="email" 
             id="email" 
             value="<?= $templateData['valid']['email']; ?>" 
             placeholder="Введите e-mail">

      <?= addRequiredSpan($templateData['errors'], 'email', 'E-mail введён некорректно'); ?>

    </div>

    <div class="form__row">
      <label class="form__label" for="password">Пароль <sup>*</sup></label>

      <input class="form__input <?= setClassError($templateData['errors'], 'password'); ?>"
             type="password" name="password" 
             id="password"
             value="<?= $templateData['valid']['password']; ?>" 
             placeholder="Введите пароль">

      <? addRequiredSpan($templateData['errors'], 'password', 'Пароль введён некорректно'); ?>
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
