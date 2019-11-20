<?= include_template('_projects-list.php', [
    'projects' => $projects,
    'current_project_id' => $current_project_id,
]); ?>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="get">
        <input class="search-form__input" type="text" name="search" value="<?= $search; ?>" placeholder="Поиск по задачам">
        <input class="search-form__submit" type="submit" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <?php foreach ($filters as $key => $value): ?>
            <a <?= ($key === $current_filter) ? '' : 'href="/?'.build_query(['filter' => $key]).'"'; ?> class="tasks-switch__item<?= ($key === $current_filter) ? ' tasks-switch__item--active' : ''; ?>"><?= $value; ?></a>
            <?php endforeach; ?>
        </nav>

        <label class="checkbox">
            <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?= $show_completed_tasks ? 'checked' : ''; ?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <?php if (!empty($search) && count($tasks) === 0): ?>
    <p class="msg">По вашему запросу ничего не найдено</p>
    <?php elseif (count($tasks) === 0): ?>
    <p class="msg">Задачи не найдены</p>
    <?php else: ?>
    <table class="tasks">
        <?php foreach ($tasks as $task): ?>
            <tr class="tasks__item task<?= $task['is_completed'] ? ' task--completed' : ''; ?><?= (!$task['is_completed'] && check_deadline_approach($task['deadline'])) ? ' task--important' : ''; ?>">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="<?= $task['id']; ?>"
                        <?= $task['is_completed'] ? 'checked' : ''; ?>>
                        <span class="checkbox__text"><?= htmlspecialchars($task['title']); ?></span>
                    </label>
                </td>
                <td class="task__file">
                    <?php if (!empty($task['file_link'])): ?>
                    <a class="download-link" href="<?= $file_path.$task['file_link']; ?>"><?= $task['file_name']; ?></a>
                    <?php endif; ?>
                </td>
                <td class="task__date">
                    <?= empty($task['deadline']) ? '' : htmlspecialchars($task['deadline']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</main>
