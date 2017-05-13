<?php
/**
 * Шаблон принимает массив параметров,
 * array $templateData['errors'] содержит массив ошибок для каждого поля,
 * array $templateData['valid'] массив валидных полей
 * array $templateData['projects'] массив проектов для поля select
 */

/**
 * Возвращает значение, которое нужно записать в value для инпута.
 * Либо по умолчанию (пустота), либо то что пришло в $_POST и нужно сохранить для невалидной формы.
 * @param type $templateData
 * @param type $name
 * @return string
 */
function getFormValue($templateData, $name)
{   
    if ($name == 'project') {
        $result = 'Выберите проект';
         var_dump($templateData['project']);
        if ($templateData['project']) {
            $result = $templateData['project'];
        }
        return $result;
    }
    // Для остальных полей берем просто что пришло в посте
    return $templateData['valid'][$name];
}
?>
<div class="modal">
  <a href="/" class="modal__close">Закрыть</a>

  <h2 class="modal__heading">Добавление задачи</h2>

  <form class="form" class="" action="/index.php" method="post" enctype="multipart/form-data">
    <div class="form__row">
      <label class="form__label" for="task">Название <sup>*</sup></label>
      <?= addRequiredSpan($templateData['errors'], 'task'); ?>
      <input class="form__input <?= setClassError($templateData['errors'], 'task'); ?>"
             type="text"
             name="task" 
             id="name" 
             value="<?= getFormValue($templateData, 'task');?>" 
             placeholder="Введите название">
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>
      <?= addRequiredSpan($templateData['errors'], 'project'); ?>

      <select class="form__input form__input--select  <?= setClassError($templateData['errors'], 'project'); ?>" name="project" id="project">
        <?php
        $selectedValue = getFormValue($templateData, 'project');
        /*Удаляю значение ключа Все*/
        array_shift($templateData['projects']);
        $allOptions = array_merge([0 => 'Выберите проект'], array_combine($templateData['projects'], $templateData['projects']));
        foreach ($allOptions as $value => $option) {
            $selected = $option == $selectedValue ? 'selected' : '';
            echo '<option value="' . $value . '" ' . $selected . '>' . $option . '</option>';
        }
        ?>
      </select>
    </div>

    <div class="form__row">
      <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>
      <?= addRequiredSpan($templateData['errors'], 'deadline'); ?>
      <input class="form__input form__input--date <?= setClassError($templateData['errors'], 'deadline'); ?>" 
             type="text" 
             name="deadline" 
             id="deadline" 
             value="<?= getFormValue($templateData, 'deadline');?>" 
             placeholder="Введите дату в формате ДД.ММ.ГГГГ">
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
      <input class="button" type="submit" name="send" value="Добавить">
    </div>
  </form>
</div>

