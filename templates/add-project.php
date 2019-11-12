<?= include_template('_projects-list.php', [
    'projects' => $projects,
    'current_project_id' => $current_project_id,
]); ?>

<main class="content__main">
    <h2 class="content__main-heading">Добавление проекта</h2>

    <form class="form" action="add-project.php" method="post" autocomplete="off">
        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>
            <input class="form__input<?= isset($errors['name']) ? ' form__input--error' : ''; ?>" type="text" name="name" id="project_name" value="<?= empty($data['name']) ? '' : $data['name']; ?>" placeholder="Введите название проекта">
            <?php if (isset($errors['name'])): ?>
            <p class="form__message"><?= $errors['name']; ?></p>
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
