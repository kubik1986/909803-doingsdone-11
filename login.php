<?php

require_once 'init.php';
require_once 'validation_functions.php';

if (!empty($user)) {
    header('Location: /');
    exit();
}

// Массив сообщений об ошибках валидации
$errors = [];

// Массив данных из формы
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Если была отправлена форма
    $fields = ['email', 'password'];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $data[$field] = trim($_POST[$field]);
        } else {
            $data[$field] = '';
        }
    }

    $rules = [
        'email' => [
            'validate_filled' => [$data['email']],
            'validate_email' => [$data['email']],
        ],
        'password' => [
            'validate_filled' => [$data['password']],
        ],
    ];

    $errors = validate($rules);

    if (empty($errors)) { // Если не было ошибок валидации
        $user = db_get_user($link, ['email' => $data['email']]);

        if (
            !empty($user)
            && password_verify($data['password'], $user['password'])
        ) {
            $_SESSION['user'] = $user;
            header('Location: /');
            exit();
        } else {
            $user = [];
            $errors['authorization'] = 'Вы ввели неверный e-mail/пароль';
        }
    }
}

$page_content = include_template('login.php', [
    'errors' => $errors,
    'data' => $data,
]);
$layout_content = include_template('layout.php', [
    'title' => $config['sitename'].': вход',
    'content' => $page_content,
    'user' => $user,
    'include_scripts' => false,
]);
echo $layout_content;
