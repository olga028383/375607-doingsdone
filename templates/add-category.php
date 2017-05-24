<?php
/**
 * Шаблон принимает массив параметров,
 * array $templateData['errors'] содержит массив ошибок для каждого поля,
 * array $templateData['valid'] массив валидных полей
 * array $templateData['projects'] массив проектов для поля select
 */
?>
<div class="modal">
    <a href="/" class="modal__close">Закрыть</a>

    <h2 class="modal__heading">Добавление категории</h2>

    <form class="form" class="" action="/index.php" method="post">
        <div class="form__row">
            <label class="form__label" for="task">Название <sup>*</sup></label>
            <?= addRequiredSpan($templateData['errors'], 'task'); ?>
            <input class="form__input <?= setClassError($templateData['errors'], 'task'); ?>"
                   type="text"
                   name="task"
                   id="name"
                   value="<?= $templateData['valid']['task'];?>"
                   placeholder="Введите название">
        </div>
        <div class="form__row ">
            <input class="button" type="submit" name="sendCategory" value="Добавить">
        </div>
    </form>
</div>

