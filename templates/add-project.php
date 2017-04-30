<?php
/**
 * Шаблон принимает массив параметров,
 * array $templateData['errors'] содержит массив ошибок для каждого поля,
 * array $templateData['allTasks'] массив состоящий из полного списка задач,для формирования выподающего списка
 */
?>
<div class="modal">
  <button class="modal__close" type="button" name="button">Закрыть</button>

  <h2 class="modal__heading">Добавление задачи</h2>

  <form class="form" class="" action="/index.php" method="post">
    <div class="form__row">
      <label class="form__label" for="task">Название <sup>*</sup></label>
      <?php
      $errorStyleName = setClassError($templateData['errors']['task']);
      addRequiredSpan($errorStyleName);
      ?>

      <input class="form__input <?= $errorStyleName; ?>" type="text" name="task" id="name" value="<?= $templateData['newTask']['task']; ?>" placeholder="Введите название">
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>
      <?php
      $errorStyleProject = setClassError($templateData['errors']['project']);
      addRequiredSpan($errorStyleProject);
      $select = 'Выберите проект';
      if ($templateData['newTask']['project']) {
          $select = $templateData['newTask']['project'];
      }
      ?>

      <select class="form__input form__input--select  <?= $errorStyleProject; ?>" name="project" id="project" value="пооп">
        <option value="<?= $templateData['newTask']['project']; ?>" selected ><?= $select ?></option>
        <?php foreach ($templateData['allTasks'] as $key => $val): ?>
            <option value="<?= $val; ?>"><?= $val; ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form__row">
      <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>
      <?php
      $errorStyleDate = setClassError($templateData['errors']['date']);
      addRequiredSpan($errorStyleDate);
      ?>
      <input class="form__input form__input--date <?= $errorStyleDate; ?>" type="text" name="date" id="date" value="<?= $templateData['newTask']['date']; ?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
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

