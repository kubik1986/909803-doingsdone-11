<?php

require_once 'init.php';

if (!empty($user)) {
    header('Location: /');
    exit();
}

$page_content = include_template('guest.php', []);
$layout_content = include_template('layout.php', [
    'title' => 'Дела в порядке',
    'content' => $page_content,
    'user' => $user,
    'include_scripts' => false,
]);
echo $layout_content;
