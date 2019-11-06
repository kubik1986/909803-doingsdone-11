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
 * Отрисовывает страницу ошибки по указанным HTTP-коду и тексту ошибки.
 *
 * @param string $http_code  Код состояния HTTP
 * @param string $message    Текст сообщения об ошибке
 * @param array  $user       Массив данных пользователя
 * @param string $page_title Заголовок страницы
 */
function show_error(string $http_code, string $message, array $user, string $page_title): void
{
    $http_codes = [
        '401' => ['title' => '401 - Требуется авторизация',
                  'header' => 'HTTP/1.1 401 Unauthorized', ],
        '403' => ['title' => '403 - Доступ запрещен',
                  'header' => 'HTTP/1.1 403 Forbidden', ],
        '404' => ['title' => '404 - Страница не найдена',
                  'header' => 'HTTP/1.1 404 Not Found', ],
        '500' => ['title' => '500 - Внутренняя ошибка сервера',
                  'header' => 'HTTP/1.1 Internal Server Error', ],
    ];
    $error_title = isset($http_codes[$http_code]) ? $http_codes[$http_code]['title'] : $http_codes['404']['title'];
    $header = isset($http_codes[$http_code]) ? $http_codes[$http_code]['header'] : $http_codes['404']['header'];
    $error = [
        'title' => $error_title,
        'message' => $message,
    ];
    header($header);
    $page_content = include_template('error.php', ['error' => $error]);
    $layout_content = include_template('layout.php', [
        'title' => $page_title,
        'content' => $page_content,
        'user' => $user,
        'include_scripts' => false,
    ]);
    echo $layout_content;
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
function build_query(array $new_params): string
{
    $params = $_GET;

    return http_build_query(array_replace($params, $new_params));
}
