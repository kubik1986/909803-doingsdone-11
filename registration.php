<?php

require_once 'init.php';
require_once 'validation_functions.php';
require_once 'config/timezones.php';

if (!empty($user)) {
    header('Location: /');
    exit();
}

// Массив сообщений об ошибках валидации
$errors = [];

// Массив данных из формы
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Если была отправлена форма
    $fields = ['email', 'password', 'name', 'timezone'];

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
            'validate_email_is_registered' => [
                $link,
                $data['email'],
            ],
        ],
        'password' => [
            'validate_filled' => [$data['password']],
            'validate_str_length' => [
                $data['password'],
                $config['forms_limits']['password_min_length'],
                $config['forms_limits']['password_max_length'],
            ],
        ],
        'name' => [
            'validate_filled' => [$data['name']],
            'validate_str_length' => [
                $data['name'],
                1,
                $config['forms_limits']['user_name_max_length'],
            ],
        ],
        'timezone' => [
            'validate_filled' => [$data['timezone']],
            'validate_timezone_id' => [
                $data['timezone'],
                $timezones,
            ],
        ],
    ];

    $errors = validate($rules);

    if (empty($errors)) { // Если не было ошибок валидации
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $user_id = db_add_user($link, $data);
        header('Location: login.php');
        exit();
    }
}

$page_content = include_template('registration.php', [
    'errors' => $errors,
    'data' => $data,
    'timezones' => $timezones,
]);
$layout_content = include_template('layout.php', [
    'title' => $config['sitename'].': регистрация',
    'content' => $page_content,
    'user' => $user,
    'include_scripts' => false,
]);
echo $layout_content;
