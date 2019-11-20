<?php

$config = [
    'sitename' => 'Дела в порядке',  //название сайта
    'timezone' => 'UTC',  //временная зона
    'user_files_path' => 'uploads/files/',  //путь к загружаемым файлам
    'filters' => [  //фильтры задач
        'all' => 'Все задачи',
        'today' => 'Повестка дня',
        'tomorrow' => 'Завтра',
        'overdue' => 'Просроченные',
    ],
    'forms_limits' => [
        'title_min_length' => 3,  // Минимальное кол-во символов в названиях/заголовках
        'title_max_length' => 100,  // Максимальное кол-во символов в названиях/заголовках
        'user_name_max_length' => 100,  // Максимальное кол-во символов в имени пользователя
        'max_file_size' => 2,  // Максимальный размер загружаемого файла, МБ
        'mime_types' => [  // Допустимые типы файлов
            'image/jpeg' => 'jpg, jpeg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.ms-excel' => 'xls',
        ],
        'password_min_length' => 6,  // Минимальная длина пароля
        'password_max_length' => 72,  // Максимальная длина пароля
    ],
];
