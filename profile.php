<?php

require_once 'init.php';
require_once 'validation_functions.php';
require_once 'config/timezones.php';

if (empty($user)) {
    show_error('401', 'Добавление проектов доступно только авторизованным пользователям. Пожалуйста, войдите в свой аккаунт, если у вас уже есть учетная запись, или зарегистрируйтесь.', $user, $config['sitename'].': добавление проекта');
    exit();
}

// Массив сообщений об ошибках валидации
$errors = [];

// Массив данных из формы
$data = [];
$data['name'] = $user['name'];
$data['timezone'] = $user['timezone'];

// Массив проектов
$projects = db_get_projects($link, $user['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Если была отправлена форма
    $fields = ['name', 'timezone', 'old_password', 'password'];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $data[$field] = trim($_POST[$field]);
        } else {
            $data[$field] = '';
        }
    }

    $rules = [
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

    if (!empty($data['password'])) {
        $rules['password'] = [
            'validate_str_length' => [
                $data['password'],
                $config['forms_limits']['password_min_length'],
                $config['forms_limits']['password_max_length'],
            ],
        ];
        $rules['old_password'] = [
            'validate_filled' => [$data['old_password']],
            'validate_password' => [
                $data['old_password'],
                $user['password'],
            ],
        ];
    }

    $errors = validate($rules);

    if (empty($errors)) { // Если не было ошибок валидации
        $user_id = $user['id'];
        $data['user_id'] = $user_id;
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        db_update_user($link, $user_id, $data);

        $user = db_get_user($link, ['id' => $user_id]);
        $_SESSION['user'] = $user;

        header('Location: profile.php?success');
        exit();
    }
}

$page_content = include_template('profile.php', [
    'projects' => $projects,
    'current_project_id' => null,
    'user' => $user,
    'timezones' => $timezones,
    'errors' => $errors,
    'data' => $data,
]);
$layout_content = include_template('layout.php', [
    'title' => $config['sitename'].': профиль пользователя',
    'content' => $page_content,
    'user' => $user,
    'include_scripts' => false,
]);
echo $layout_content;
