<?php
/**
 * Шаблон принимает массив параметров,
 * array $templateData['projects'] содержит список проектов,
 * array $templateData['allTasks'] массив состоящий из полного списка задач,
 * array $templateData['tasksToDisplay'] новый массив состоящий из задач, запрошенных пользователем*
 */
?>
<div class="content">
    <section class="content__side">
        <h2 class="content__side-heading">Проекты</h2>

        <nav class="main-navigation">
            <ul class="main-navigation__list">
                <?php
                $keyToHightlight = !empty($_GET['project']) ? $_GET['project'] : 0;
                $newArray = array_merge([['id' => 0, 'name' => 'Все']], $templateData['projects']);
                foreach ($newArray as $val):
                    $activeClass = '';
                    if ($val['id'] == $keyToHightlight) {
                        $activeClass = "main-navigation__list-item--active";
                    }
                    ?>
                    <li class="main-navigation__list-item <?= $activeClass; ?>">
                        <a class="main-navigation__list-item-link"
                           href="/index.php?project=<?= $val['id']; ?>"><?= htmlspecialchars($val['name']); ?></a>
                        <span class="main-navigation__list-item-count"><?= getNumberTasks($templateData['allTasks'], htmlspecialchars($val['id'])); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <a class="button button--transparent button--plus content__side-button" href="/index.php?addCategory">Добавить
            проект</a>
    </section>

    <main class="content__main">
        <h2 class="content__main-heading">Список задач</h2>

        <form class="search-form" action="index.php?search" method="get">
            <input class="search-form__input" type="text" name="search" value="" placeholder="Поиск по задачам">

            <input class="search-form__submit" type="submit" name="" value="Искать">
        </form>

        <div class="tasks-controls">
            <nav class="tasks-switch">
                <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
                <a href="/index.php?filter=today" class="tasks-switch__item">Повестка дня</a>
                <a href="/index.php?filter=tomorrow" class="tasks-switch__item">Завтра</a>
                <a href="/index.php?filter=overdue" class="tasks-switch__item">Просроченные</a>
            </nav>
            <?php
            $checked = '';
            $hidden = 'hidden';
            // в зависимости от праметра,сохраненного в куки показываемили скрываем чекбокс,
            //  а так же определяем переменную hidden (скрыть показать задачу)
            if ($templateData['show_completed']) {
                $checked = 'checked';
                $hidden = '';
            }
            ?>
            <label class="checkbox">
                <input id="show-complete-tasks" class="checkbox__input visually-hidden" <?= $checked; ?>
                       type="checkbox">
                <span class="checkbox__text">Показывать выполненные</span>
            </label>
        </div>

        <table class="tasks">
            <?php
            foreach ($templateData['tasksToDisplay'] as $key => $val):
                $taskCompleted = '';
                $showCompletedTasks = '';
                $hiddenButtonComplete = '';
                $hiddenFieldFile = '';
                // определяем переменную и показываем в зависимости от параметра куки
                // только завершенные задачи
                if (!empty($val['complete'])) {
                    $taskCompleted = 'task--completed';
                    $showCompletedTasks = $hidden;
                    $hiddenButtonComplete = 'hidden';
                }
                if (empty($val['file'])) {
                    $hiddenFieldFile = 'hidden';
                }
                ?>
                <tr class="tasks__item task <?= $taskCompleted; ?>" <?= $showCompletedTasks; ?> >
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input class="checkbox__input visually-hidden" type="checkbox">
                            <span class="checkbox__text"><?= htmlspecialchars($val['task']); ?></span>
                        </label>
                    </td>
                    <td class="task__file">
                        <a class="download-link" <?= $hiddenFieldFile; ?> href="<?= $val['file']; ?>">Home.psd</a>
                    </td>
                    <td class="task__date"><?= htmlspecialchars($val['deadline']); ?></td>
                    <td class="task__controls">
                        <button class="expand-control" type="button" name="button">Открыть список комманд</button>

                        <ul class="expand-list hidden">
                            <li class="expand-list__item" <?= $hiddenButtonComplete; ?>>
                                <a href="/complete-task.php?id=<?= $val['id']; ?>">Выполнить</a>
                            </li>

                            <li class="expand-list__item">
                                <a href="/delete-task.php?id=<?= $val['id']; ?>">Удалить</a>
                            </li>

                            <li class="expand-list__item">
                                <a href="/duplicate-task.php?id=<?= $val['id']; ?>">Дублировать</a>
                            </li>
                        </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</div>