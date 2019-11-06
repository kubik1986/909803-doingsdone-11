<?php

require_once 'config/config.php';
require_once 'config/db.php';
require_once 'functions.php';
require_once 'db-functions.php';

$user = [
    'id' => 1,
    'name' => 'John Doe',
    'email' => 'john_doe@gmail.com',
    'password' => '$2y$10$Nbn97Bc6Rzdk1POIvmBPcuD14T/FpLa0.DTOclYwebMJcA5uZgWi',
];

// Подключение к БД
$link = db_connect($db);
mysqli_set_charset($link, 'utf8');

// Установка временной зоны
date_default_timezone_set($config['timezone']);
