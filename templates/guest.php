<?php
/**
 * Шаблон принимает объект $authForm,
 */
$form = $templateData['form'];
$valid = $form->getformData();

$showModalAuth = '';
$messageAfterRegistered = '';
if (!$templateData['showAuthenticationForm']) {
    $showModalAuth = 'hidden';
}
if ($templateData['messageAfterRegistered']) {
    $messageAfterRegistered = "<div style='margin-bottom: 20px; text-align: center'>Теперь вы можете войти, используя свой email и пароль</div>";
}
?>

<div class="content">
  <section class="welcome">
    <h2 class="welcome__heading">«Дела в порядке»</h2>

    <div class="welcome__text">
      <p>«Дела в порядке» — это веб приложение для удобного ведения списка дел. Сервис помогает пользователям не забывать о предстоящих важных событиях и задачах.</p>

      <p>После создания аккаунта, пользователь может начать вносить свои дела, деля их по проектам и указывая сроки.</p>
    </div>

    <a class="welcome__button button" href="/register.php">Зарегистрироваться</a>
  </section>
</div>

<div class="modal" <?= $showModalAuth; ?> >
  <a href="/" class="modal__close" name="button">Закрыть</a>

  <h2 class="modal__heading">Вход на сайт</h2>
  <?= $messageAfterRegistered; ?>
  <form class="form" class="" action="index.php" method="post">
    <div class="form__row">
      <label class="form__label" for="email">E-mail <sup>*</sup></label>
        <?= addRequiredSpan($form->getError('email')); ?>
      <input class="form__input <?php if ($form->getError('email')): ?>form__input--error <?php endif;?>"
             type="text" 
             name="auth[email]"
             id="email" 
             value="<?php $valid ? print(htmlspecialchars($valid['email'])) : ''; ?>"
             placeholder="Введите e-mail">

    </div>

    <div class="form__row">
      <label class="form__label" for="password">Пароль <sup>*</sup></label>
        <?= addRequiredSpan($form->getError('password')); ?>
      <input class="form__input <?php if ($form->getError('password')): ?>form__input--error <?php endif;?>"
             type="password"
             name="auth[password]"
             id="password"
             value="<?php $valid ? print(htmlspecialchars($valid['password'])) : ''; ?>"
             placeholder="Введите пароль">

    </div>

    <div class="form__row">
      <label class="checkbox">
        <input class="checkbox__input visually-hidden" type="checkbox" checked>
        <span class="checkbox__text">Запомнить меня</span>
      </label>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" value="Войти">
    </div>
  </form>
</div>
