<?php
/**
 * Шаблон принимает объект $form,
 */
$form = $templateData['form'];
$valid = $form->getformData();
?>
<div class="content">
    <section class="content__side">
        <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

        <a class="button button--transparent content__side-button" href="/login.php">Войти</a>
    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Регистрация аккаунта</h2>

        <form class="form" action="register.php" method="post">
            <div class="form__row">
                <label class="form__label" for="email">E-mail <sup>*</sup></label>

                <!--Выводим сообшения в зависимости от корректности введенного email  или существования пользователя-->
                <?= addRequiredSpan($form->getError('email')); ?>
                <input class="form__input <?php if ($form->getError('email')): ?>form__input--error <?php endif; ?>"
                       type="text"
                       name="signup[email]"
                       id="email"
                       value="<?php $valid ? print(htmlspecialchars($valid['email'])) : ''; ?>"
                       placeholder="Введите e-mail">
            </div>

            <div class="form__row">
                <label class="form__label" for="password">Пароль <sup>*</sup></label>
                <?= addRequiredSpan($form->getError('password')); ?>
                <input class="form__input <?php if ($form->getError('password')): ?>form__input--error <?php endif; ?>"
                       type="password"
                       name="signup[password]"
                       id="password"
                       value="<?php $valid ? print(htmlspecialchars($valid['password'])) : ''; ?>"
                       placeholder="Введите пароль">

            </div>
            <div class="form__row">
                <label class="form__label" for="name">Имя <sup>*</sup></label>
                <?= addRequiredSpan($form->getError('name')); ?>
                <input class="form__input <?php if ($form->getError('name')): ?>form__input--error <?php endif; ?>"
                       name="signup[name]"
                       id="name"
                       value="<?php $valid ? print(htmlspecialchars($valid['name'])) : ''; ?>"
                       placeholder="Введите имя">

            </div>

            <div class="form__row form__row--controls">

                <input class="button" type="submit" value="Зарегистрироваться">
            </div>
        </form>
    </main>
</div>

