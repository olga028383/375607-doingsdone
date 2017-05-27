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
$form = $templateData['form'];
$valid = $form->getformData();
function getFormValue($templateData, $name)
{
    $isField = $templateData ? $templateData : '';
    if(!$isField){
        return;
    }
   if ($name == 'project') {
        $result = '0';
        if ($templateData['project']) {
            $result = $templateData['project'];
            
        }
        return $result;
    }
    // Для остальных полей берем просто что пришло в посте
    return $templateData[$name];
}
?>
<div class="modal">
  <a href="/" class="modal__close">Закрыть</a>

  <h2 class="modal__heading">Добавление задачи</h2>

  <form class="form" class="" action="/index.php" method="post" enctype="multipart/form-data">
    <div class="form__row">
      <label class="form__label" for="task">Название <sup>*</sup></label>
        <?= addRequiredSpan($form->getError('task')); ?>
      <input class="form__input <?php if ($form->getError('task')): ?>form__input--error <?php endif;?>"
             type="text"
             name="task[task]"
             id="name" 
             value="<?=getFormValue($valid, 'task') ?>"
             placeholder="Введите название">
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>
        <?= addRequiredSpan($form->getError('project')); ?>

      <select class="form__input form__input--select <?php if ($form->getError('project')): ?>form__input--error <?php endif;?>" name="task[project]" id="project">
        <?php
        $selectedValue = getFormValue($valid, 'project');
        $allOptions = array_merge([['id' =>0,'name' =>'Выберите проект']], $templateData['projects']);
        foreach ($allOptions as $value => $option) {
            $selected = $option['id'] == $selectedValue ? 'selected' : '';
            echo '<option value="' . $option['id'] . '" ' . $selected . '>' . $option['name'] . '</option>';
        }
        ?>
      </select>
    </div>

    <div class="form__row">
      <label class="form__label" for="deadline">Дата выполнения <sup>*</sup></label>
        <?= addRequiredSpan($form->getError('deadline')); ?>
      <input class="form__input form__input--date <?php if ($form->getError('deadline')): ?>form__input--error <?php endif;?>"
             type="text" 
             name="task[deadline]"
             id="deadline" 
             value="<?=getFormValue($valid, 'deadline') ?>"
             placeholder="Введите дату в формате ДД.ММ.ГГГГ">
    </div>

    <div class="form__row">
      <label class="form__label" for="file">Файл</label>

      <div class="form__input-file">
        <input class="visually-hidden" type="file" name="task[preview]" id="preview" value="">

        <label class="button button--transparent" for="preview">
          <span>Выберите файл</span>
        </label>
      </div>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" value="Добавить">
    </div>
  </form>
</div>

