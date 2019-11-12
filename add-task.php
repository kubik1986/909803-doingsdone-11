<?php

require_once 'init.php';
require_once 'validation_functions.php';

if (empty($user)) {
    header('Location: guest.php');
    exit();
}

// Массив сообщений об ошибках валидации
$errors = [];

// Массив данных из формы
$data = [];

// Массив проектов
$projects = db_get_projects($link, $user['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Если была отправлена форма
    $fields = ['name', 'project', 'date'];

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
                $config['forms_limits']['title_min_length'],
                $config['forms_limits']['title_max_length'],
            ],
        ],
        'project' => [
            'validate_filled' => [$data['project']],
            'validate_project_id_exists' => [
                $link,
                $user['id'],
                intval($data['project']),
            ],
        ],
    ];

    if (!empty($data['date'])) {
        $rules['date'] = [
            'validate_date_format' => [$data['date']],
            'validate_date_in_past' => [$data['date']],
        ];
    }

    if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
        $rules['file'] = [
            'validate_file_size' => [
                $_FILES['file'],
                $config['forms_limits']['max_file_size'],
            ],
        ];
    }

    $errors = validate($rules);

    if (empty($errors)) { // Если не было ошибок валидации
        if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
            $file_name = $_FILES['file']['name'];
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = uniqid('user-'.$user['id'].'-').'.'.$file_extension;
            $file_dir = $config['user_files_path'];
            move_uploaded_file($_FILES['file']['tmp_name'], $file_dir.$new_file_name);

            $data['file_name'] = $file_name;
            $data['file_link'] = $new_file_name;
        }

        $data['author_id'] = $user['id'];
        $task_id = db_add_task($link, $data);

        header('Location: /');
        exit();
    }
}

$page_content = include_template('add-task.php', [
    'projects' => $projects,
    'current_project_id' => null,
    'errors' => $errors,
    'data' => $data,
]);
$layout_content = include_template('layout.php', [
    'title' => $config['sitename'].': добавление задачи',
    'content' => $page_content,
    'user' => $user,
    'include_scripts' => true,
]);
echo $layout_content;
