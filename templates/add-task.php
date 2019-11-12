<?= include_template('_projects-list.php', [
    'projects' => $projects,
    'current_project_id' => $current_project_id,
]); ?>

<main class="content__main">
    <h2 class="content__main-heading">Добавление задачи</h2>

    <form class="form" action="add-task.php" method="post" autocomplete="off" enctype="multipart/form-data">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>
            <input class="form__input<?= isset($errors['name']) ? ' form__input--error' : ''; ?>" type="text" name="name" id="name" value="<?= empty($data['name']) ? '' : $data['name']; ?>" placeholder="Введите название">
            <?php if (isset($errors['name'])): ?>
            <p class="form__message"><?= $errors['name']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>
            <select class="form__input form__input--select<?= isset($errors['project']) ? ' form__input--error' : ''; ?>" name="project" id="project">
                <option value="" selected disabled>Выберите проект</option>
                <?php foreach ($projects as $project): ?>
                <option value="<?= $project['id']; ?>" <?= (!empty($data['project']) && intval($data['project']) === $project['id']) ? 'selected' : ''; ?>><?=$project['title']; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['project'])): ?>
            <p class="form__message"><?= $errors['project']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>
            <input class="form__input form__input--date<?= isset($errors['date']) ? ' form__input--error' : ''; ?>" type="text" name="date" id="date" value="<?= empty($data['date']) ? '' : $data['date']; ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
            <?php if (isset($errors['date'])): ?>
            <p class="form__message"><?= $errors['date']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="file">Файл</label>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="file" id="file" value="">
                <label class="button button--transparent" for="file">
                    <span>Выберите файл</span>
                </label>
            </div>
            <?php if (isset($errors['file'])): ?>
            <p class="form__message"><?= $errors['file']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row form__row--controls">
            <?php if (!empty($errors)): ?>
            <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
            <?php endif; ?>
            <input class="button" type="submit" name="submit" value="Добавить">
        </div>
    </form>
</main>
