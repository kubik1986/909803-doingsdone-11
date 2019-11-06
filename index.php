<?php

require_once 'init.php';

if (empty($user)) {
    header('Location: guest.php');
    exit();
}

// показывать или нет выполненные задачи
$show_completed_tasks = isset($_GET['show_completed']) ? intval($_GET['show_completed']) : 0;

// id текущего проекта
$current_project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

// фильтр задач
$filters = $config['filters'];
$current_filter = isset($_GET['filter']) ? $_GET['filter'] : array_keys($filters)[0];
if (!array_key_exists($current_filter, $filters)) {
    $current_filter = array_keys($filters)[0];
}

// Массив проектов
$projects = db_get_projects($link, $user['id'], $current_filter);

// Масив задач
$tasks = db_get_tasks($link, $user['id'], $current_project_id, $current_filter);

$page_content = include_template('main.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'current_project_id' => $current_project_id,
    'filters' => $filters,
    'current_filter' => $current_filter,
    'file_path' => $config['user_files_path'],
    'show_completed_tasks' => $show_completed_tasks,
]);
$layout_content = include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $page_content,
    'user' => $user,
    'include_scripts' => true,
]);
echo $layout_content;
