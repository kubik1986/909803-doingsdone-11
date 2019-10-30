<?php
/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 *
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array  $data Ассоциативный массив с данными для шаблона
 *
 * @return string Итоговый HTML
 */
function include_template(string $name, array $data = []): string
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
function count_number_of_tasks(array $tasks, string $project_title): int
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
 * @return bool true, если текущая страница является главной, иначе false
 */
function check_main_page(): bool
{
    return isset($_SERVER['REQUEST_URI'])
                 && ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php');
}

/**
 * Определяет, является ли количество часов, оставшихся до определенной даты, меньше либо равно указанному значению.
 *
 * @param string $deadline_date         Дата, до которой вычисляется количество оставшихся часов
 * @param int    $max_hours_to_deadline Количество часов для сравнения
 *
 * @return bool true, если количество оставшихся часов меньше либо равно сравниваемому значению, иначе false
 */
function check_deadline_approach(?string $deadline_date, int $max_hours_to_deadline = 24): bool
{
    if (is_null($deadline_date)) {
        return false;
    }

    $deadline_time = strtotime($deadline_date);
    $seconds_to_deadline = $deadline_time - time();
    $hours_to_deadline = (int) floor($seconds_to_deadline / 3600);

    return $hours_to_deadline <= $max_hours_to_deadline;
}

/**
 * Генерирует строку запроса путем добавления указанных свойств либо их замены в текущей строке запроса.
 *
 * @param array $new_params Массив свойств, которые будут добавлены к текущей строке запроса или будут заменены на новые значения, если уже присутствуют в текущей строке запроса
 *
 * @return string Строка запроса
 */
function build_query(array $new_params)
{
    $params = $_GET;

    return http_build_query(array_replace($params, $new_params));
}
