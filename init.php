<?php

require_once 'config/config.php';
require_once 'config/db.php';
require_once 'functions.php';
require_once 'db-functions.php';

// Старт сессии
session_start();
$user = isset($_SESSION['user']) ? $_SESSION['user'] : [];

// Подключение к БД
$link = db_connect($db);
mysqli_set_charset($link, 'utf8');

// Установка временной зоны
date_default_timezone_set($config['timezone']);
