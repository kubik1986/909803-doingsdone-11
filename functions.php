<?php
/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 *
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array  $data Ассоциативный массив с данными для шаблона
 *
 * @return string Итоговый HTML
 */
function include_template($name, array $data = [])
{
    $name = 'templates/'.$name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Подсчитывает количество задач для указанного проекта.
 *
 * @param array  $tasks         Массив задач
 * @param string $project_title Название проекта
 *
 * @return int Количестыо задач
 */
function count_number_of_tasks($tasks, $project_title)
{
    $count = 0;

    foreach ($tasks as $task) {
        if ($task['project'] === $project_title) {
            ++$count;
        }
    }

    return $count;
}

/**
 * Определяет, является ли текущая страница главной.
 *
 * @return bool
 */
function check_main_page()
{
    return isset($_SERVER['REQUEST_URI'])
                 && ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php');
}
