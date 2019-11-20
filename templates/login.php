<section class="content__side">
    <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>
    <a class="button button--transparent content__side-button" href="login.php">Войти</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Вход на сайт</h2>

    <form class="form" action="login.php" method="post" autocomplete="off">
        <div class="form__row">
            <label class="form__label" for="email">E-mail <sup>*</sup></label>
            <input class="form__input<?= isset($errors['email']) ? ' form__input--error' : ''; ?>" type="text" name="email" id="email" value="<?= empty($data['email']) ? '' : $data['email']; ?>" placeholder="Введите e-mail">
            <?php if (isset($errors['email'])): ?>
            <p class="form__message"><?= $errors['email']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>
            <input class="form__input<?= isset($errors['password']) ? ' form__input--error' : ''; ?>" type="password" name="password" id="password" value="<?= empty($data['password']) ? '' : $data['password']; ?>" placeholder="Введите пароль">
            <?php if (isset($errors['password'])): ?>
            <p class="form__message"><?= $errors['password']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row form__row--controls">
            <?php if (!empty($errors) && empty($errors['authorization'])): ?>
            <p class="error-message">Пожалуйста, исправьте ошибки в форме</p>
            <?php elseif (!empty($errors['authorization'])): ?>
            <p class="error-message"><?= $errors['authorization']; ?></p>
            <?php endif; ?>
            <input class="button" type="submit" value="Войти">
        </div>
    </form>
</main>
