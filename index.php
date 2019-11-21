<?php

require_once 'init.php';

if (empty($user)) {
    header('Location: guest.php');
    exit();
}

// Обновление статуса задачи
if (!empty($_GET['complete_task'])) {
    $request = trim($_GET['complete_task']);
    $request = explode('_', $request);

    if (count($request) === 2) {
        $task_id = intval($request[0]);
        $status = intval($request[1]) >= 1 ? 1 : 0;

        if (db_is_task_exist($link, $user['id'], $task_id)) {
            db_update_task_status($link, $task_id, $status);
        } else {
            show_error('404', 'По вашему запросу ничего не найдено.', $user, $config['sitename']);
            exit();
        }
    }

    $location = '/';
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'http://'.$_SERVER['SERVER_NAME']) === 0) {
        $location = $_SERVER['HTTP_REFERER'];
    }
    header("Location: $location");
    exit();
}

// Показывать или нет выполненные задачи
$show_completed_tasks = intval($_GET['show_completed'] ?? 0);

// ID текущего проекта
$current_project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : null;

// Фильтр задач
$filters = $config['filters'];
$current_filter = $_GET['filter'] ?? array_keys($filters)[0];
if (
    (
        !is_null($current_project_id)
        && !db_is_project_exist($link, $user['id'], ['id' => $current_project_id])
    )
    || !array_key_exists($current_filter, $filters)
) {
    show_error('404', 'По вашему запросу ничего не найдено.', $user, $config['sitename']);
    exit();
}

// Поисковый запрос
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Устанавливает временную зону пользователя
date_default_timezone_set($user['timezone']);
db_set_time_zone($link, $user['timezone']);

// Массив проектов
$projects = db_get_projects($link, $user['id'], $current_filter, $show_completed_tasks);

// Масив задач
$tasks = db_get_tasks($link, $user['id'], $current_project_id, $current_filter, $show_completed_tasks, $search);

$page_content = include_template('main.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'current_project_id' => $current_project_id,
    'filters' => $filters,
    'current_filter' => $current_filter,
    'file_path' => $config['user_files_path'],
    'show_completed_tasks' => $show_completed_tasks,
    'search' => $search,
]);
$layout_content = include_template('layout.php', [
    'title' => $config['sitename'],
    'content' => $page_content,
    'user' => $user,
    'include_scripts' => true,
]);
echo $layout_content;
