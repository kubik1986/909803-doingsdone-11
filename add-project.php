<?php

require_once 'init.php';
require_once 'validation_functions.php';

if (empty($user)) {
    show_error('401', 'Добавление проектов доступно только авторизованным пользователям. Пожалуйста, войдите в свой аккаунт, если у вас уже есть учетная запись, или зарегистрируйтесь.', $user, $config['sitename'].': добавление проекта');
    exit();
}

// Массив сообщений об ошибках валидации
$errors = [];

// Массив данных из формы
$data = [];

// id текущего проекта
$current_project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : null;

// Массив проектов
$projects = db_get_projects($link, $user['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Если была отправлена форма
    $data['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
    $rules = [
        'name' => [
            'validate_filled' => [$data['name']],
            'validate_str_length' => [
                $data['name'],
                $config['forms_limits']['title_min_length'],
                $config['forms_limits']['title_max_length'],
            ],
            'validate_project_title_exists' => [
                $link,
                $user['id'],
                $data['name'],
            ],
        ],
    ];

    $errors = validate($rules);

    if (empty($errors)) { // Если не было ошибок валидации
        $data['author_id'] = $user['id'];
        $project_id = db_add_project($link, $data);

        header("Location: add-project.php?project_id=$project_id");
        exit();
    }
}

$page_content = include_template('add-project.php', [
    'projects' => $projects,
    'current_project_id' => $current_project_id,
    'errors' => $errors,
    'data' => $data,
]);
$layout_content = include_template('layout.php', [
    'title' => $config['sitename'].': добавление проекта',
    'content' => $page_content,
    'user' => $user,
    'include_scripts' => false,
]);
echo $layout_content;
