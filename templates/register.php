<?php
/**
 * Шаблон принимает массив параметров,
 * array $templateData['errors'] содержит массив ошибок для каждого поля,
 * array $templateData['valid'] содержит массив заполненных полей
 */
?>
<div class="content">
  <section class="content__side">
    <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

    <a class="button button--transparent content__side-button" href="/index.php?login">Войти</a>
  </section>

  <main class="content__main">
    <h2 class="content__main-heading">Регистрация аккаунта</h2>

    <form class="form" class="" action="index.php" method="post">
      <div class="form__row">
        <label class="form__label" for="email">E-mail <sup>*</sup></label>
        <?php
        $valueValidEmail = '';
        $setErrorStyle = '';
        $errorEmail = '';
        if ($templateData['valid']['email'] && $templateData['user']) {
            $errorEmail = addRequiredSpan($templateData['valid'], 'email', 'Пользователь с таким email уже существует');
            $setErrorStyle = setClassError($templateData['valid'], 'email');
        } else {
            $errorEmail = addRequiredSpan($templateData['errors'], 'email', 'Email введен некорректно');
            $valueValidEmail = $templateData['valid']['email'];
            $setErrorStyle = setClassError($templateData['errors'], 'email');
        }
        ?>
        <input class="form__input <?= $setErrorStyle; ?>" 
               type="text"
               name="email" 
               id="email" 
               value="<?= $valueValidEmail; ?>" 
               placeholder="Введите e-mail">
        
        <!--Выводим сообшения в зависимости от корректности введенного email  или существования пользователя-->
        <?= $errorEmail; ?>
      </div>

      <div class="form__row">
        <label class="form__label" for="password">Пароль <sup>*</sup></label>
         <?= addRequiredSpan($templateData['errors'], 'password', 'Введите пароль'); ?>
        <input class="form__input <?= setClassError($templateData['errors'], 'password'); ?>" 
               type="password" 
               name="password" 
               id="password" 
               value="<?= $templateData['valid']['password']; ?>"
               placeholder="Введите пароль">
              
      </div>
      <div class="form__row">
        <label class="form__label" for="name">Имя <sup>*</sup></label>
        <?= addRequiredSpan($templateData['errors'], 'name', 'Введите Ваше имя'); ?>
        <input class="form__input <?= setClassError($templateData['errors'], 'name'); ?>" 
               name="name" 
               id="name" 
               value="<?= $templateData['valid']['name']; ?>"
               placeholder="Введите имя">
               
      </div>

      <div class="form__row form__row--controls">

        <input class="button" type="submit" name="register" value="Зарегистрироваться">
      </div>
    </form>
  </main>
</div>
