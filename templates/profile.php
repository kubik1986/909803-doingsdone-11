<?= include_template('_projects-list.php', [
    'projects' => $projects,
    'current_project_id' => $current_project_id,
]); ?>

<main class="content__main">
    <h2 class="content__main-heading">Профиль</h2>

    <form class="form" action="profile.php" method="post" autocomplete="off">
        <?php if (isset($_GET['success'])): ?>
        <p class="msg">Профиль успешно обновлен</p>
        <?php endif; ?>
        <div class="form__row">
            <label class="form__label" for="name">Имя <sup>*</sup></label>
            <input class="form__input<?= isset($errors['name']) ? ' form__input--error' : ''; ?>" type="text" name="name" id="name" value="<?= !empty($data['name']) ? $data['name'] : ''; ?>" placeholder="Введите имя">
            <?php if (isset($errors['name'])): ?>
            <p class="form__message"><?= $errors['name']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="timezone">Часовой пояс <sup>*</sup></label>
            <select class="form__input form__input--select<?= isset($errors['timezone']) ? ' form__input--error' : ''; ?>" name="timezone" id="timezone">
                <option value="" selected disabled>Выберите часовой пояс</option>
                <?php foreach ($timezones as $key => $value): ?>
                <option value="<?= $key; ?>" <?= (!empty($data['timezone']) && $data['timezone'] === $key) ? 'selected' : ''; ?>><?= $value; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['timezone'])): ?>
            <p class="form__message"><?= $errors['timezone']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="old_password">Старый пароль</label>
            <input class="form__input<?= isset($errors['old_password']) ? ' form__input--error' : ''; ?>" type="password" name="old_password" id="old_password" value="<?= empty($data['old_password']) ? '' : $data['old_password']; ?>">
            <?php if (isset($errors['old_password'])): ?>
            <p class="form__message"><?= $errors['old_password']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="password">Новый пароль</label>
            <input class="form__input<?= isset($errors['password']) ? ' form__input--error' : ''; ?>" type="password" name="password" id="password" value="<?= empty($data['password']) ? '' : $data['password']; ?>">
            <?php if (isset($errors['password'])): ?>
            <p class="form__message"><?= $errors['password']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row form__row--controls">
            <?php if (!empty($errors)): ?>
            <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
            <?php endif; ?>
            <input class="button" type="submit" name="submit" value="Сохранить">
        </div>
    </form>
</main>
