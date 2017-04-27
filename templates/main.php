<div class="content">
  <section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
      <ul class="main-navigation__list">
        <?php
        foreach ($array[0] as $key => $val):
            $firstItem = '';

            if ($key == 0) {
                $firstItem = "main-navigation__list-item--active";
            }
            ?>
            <li class="main-navigation__list-item <?= $firstItem; ?>">
              <a class="main-navigation__list-item-link" href="#"><?= $val; ?></a>
              <span class="main-navigation__list-item-count"><?= getNumberTasks($array[1], $val); ?></span>
            </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="#">Добавить проект</a>
  </section>

  <main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="post">
      <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

      <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
      <div class="radio-button-group">
        <label class="radio-button">
          <input class="radio-button__input visually-hidden" type="radio" name="radio" checked="">
          <span class="radio-button__text">Все задачи</span>
        </label>

        <label class="radio-button">
          <input class="radio-button__input visually-hidden" type="radio" name="radio">
          <span class="radio-button__text">Повестка дня</span>
        </label>

        <label class="radio-button">
          <input class="radio-button__input visually-hidden" type="radio" name="radio">
          <span class="radio-button__text">Завтра</span>
        </label>

        <label class="radio-button">
          <input class="radio-button__input visually-hidden" type="radio" name="radio">
          <span class="radio-button__text">Просроченные</span>
        </label>
      </div>

      <label class="checkbox">
        <input id="show-complete-tasks" class="checkbox__input visually-hidden" checked type="checkbox">
        <span class="checkbox__text">Показывать выполненные</span>
      </label>
    </div>

    <table class="tasks">
      <?php
      foreach ($array[1] as $key => $val):
          $taskCompleted = '';

          if ($val['result'] == 'Да') {
              $taskCompleted = 'task--completed';
          }
          ?>
          <tr class="tasks__item task <?= $taskCompleted; ?>">
            <td class="task__select">
              <label class="checkbox task__checkbox">
                <input class="checkbox__input visually-hidden" type="checkbox" checked>
                <span class="checkbox__text"><?= $val['task']; ?></span>
              </label>
            </td>
            <td class="task__date"><?= $val['date']; ?></td>
            <td class="task__controls"></td>
          </tr>
      <?php endforeach; ?>
    </table>
  </main>
</div>
</div>
</div>
