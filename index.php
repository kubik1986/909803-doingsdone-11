<?php

require_once 'init.php';

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
$projects = [
    [
        'id' => 1,
        'title' => 'Входящие',
    ],
    [
        'id' => 2,
        'title' => 'Учеба',
    ],
    [
        'id' => 3,
        'title' => 'Работа',
    ],
    [
        'id' => 4,
        'title' => 'Домашние дела',
    ],
    [
        'id' => 5,
        'title' => 'Авто',
    ],
];

// Масив задач
$tasks = [
    [
        'id' => 1,
        'title' => 'Собеседование в IT компании',
        'deadline' => date('d.m.Y', strtotime('tomorrow')),
        'project' => 'Работа',
        'is_completed' => false,
    ],
    [
        'id' => 2,
        'title' => 'Выполнить тестовое задание',
        'deadline' => '25.12.2019',
        'project' => 'Работа',
        'is_completed' => false,
    ],
    [
        'id' => 3,
        'title' => 'Сделать задание первого раздела',
        'deadline' => date('d.m.Y', strtotime('yesterday')),
        'project' => 'Учеба',
        'is_completed' => true,
    ],
    [
        'id' => 4,
        'title' => 'Встреча с другом',
        'deadline' => '22.12.2019',
        'project' => 'Входящие',
        'is_completed' => false,
    ],
    [
        'id' => 5,
        'title' => 'Купить корм для кота',
        'deadline' => null,
        'project' => 'Домашние дела',
        'is_completed' => false,
    ],
    [
        'id' => 6,
        'title' => 'Заказать пиццу',
        'deadline' => null,
        'project' => 'Домашние дела',
        'is_completed' => false,
    ],
];

$page_content = include_template('main.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'current_project_id' => $current_project_id,
    'filters' => $filters,
    'current_filter' => $current_filter,
    'show_completed_tasks' => $show_completed_tasks,
]);
$layout_content = include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $page_content,
    'user' => $user,
    'include_scripts' => true,
]);
echo $layout_content;
