<?php
/**
 * Шаблон принимает массив параметров,
 * array $templateData['errors'] содержит массив ошибок для каждого поля,
 * array $templateData['valid'] массив валидных полей
 * array $templateData['projects'] массив проектов для поля select
 */
$form = $templateData['form'];
$valid = $form->getformData();
?>
<div class="modal">
    <a href="/" class="modal__close">Закрыть</a>

    <h2 class="modal__heading">Добавление категории</h2>

    <form class="form" class="" action="/index.php" method="post">
        <div class="form__row">
            <label class="form__label" for="category">Название <sup>*</sup></label>
            <?= addRequiredSpan($form->getError('category')); ?>
            <input class="form__input <?php if ($form->getError('category')): ?>form__input--error <?php endif;?>"
                   type="text"
                   name="category[category]"
                   id="name"
                   value="<?php $valid ? print(htmlspecialchars($valid['category'])) : ''; ?>"
                   placeholder="Введите название">
        </div>
        <div class="form__row ">
            <input class="button" type="submit" value="Добавить">
        </div>
    </form>
</div>

