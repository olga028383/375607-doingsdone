<?php
/**
 * Шаблон принимает объект $authForm,
 */
$form = $templateData['form'];
$valid = $form->getformData();
$messageAfterRegistered = '';
if ($templateData['messageAfterRegistered']) {
    $messageAfterRegistered = "<div style='margin-bottom: 20px; text-align: center'>Теперь вы можете войти, используя свой email и пароль</div>";
}
?>

<div class="modal">
    <a href="/" class="modal__close" name="button">Закрыть</a>

    <h2 class="modal__heading">Вход на сайт</h2>
    <?= $messageAfterRegistered; ?>
    <form class="form" class="" action="login.php" method="post">
        <div class="form__row">
            <label class="form__label" for="email">E-mail <sup>*</sup></label>
            <?= addRequiredSpan($form->getError('email')); ?>
            <input class="form__input <?php if ($form->getError('email')): ?>form__input--error <?php endif; ?>"
                   type="text"
                   name="auth[email]"
                   id="email"
                   value="<?php $valid ? print(htmlspecialchars($valid['email'])) : ''; ?>"
                   placeholder="Введите e-mail">

        </div>

        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>
            <?= addRequiredSpan($form->getError('password')); ?>
            <input class="form__input <?php if ($form->getError('password')): ?>form__input--error <?php endif; ?>"
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
