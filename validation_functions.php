<?php

/**
 * Валидирует данные, переданные в формах.
 *
 * @param array $rules Ассоциативный массив, содержащий правила для валидируемых полей. Массив имеет следующий формат: ['имя_поля_1' => ['имя_функции_валидации' => ['параметр_функции_1', 'параметр_функции_2', ...], ... ], 'имя_поля_2' => [<массив_имен_функций_валидации_и_их_параметров>]], ...]
 *
 * @return array Ассоциативный массив, в который будут переданы сообщения об ошибках валидации по каждому из полей
 */
function validate(array $rules): array
{
    $errors = [];
    foreach ($rules as $field => $value) {
        foreach ($value as $cb => $args) {
            if (isset($errors[$field])) {
                break;
            }
            $result = $cb(...$args);
            if ($result) {
                $errors[$field] = $result;
            }
        }
    }

    return $errors;
}

/**
 * Проверяет, заполнено ли поле. Значения 0 или '0' считаются валидными.
 *
 * @param mixed $value Проверяемое значение
 *
 * @return string Строка сообщения об ошибке, если поле не заполнено
 */
function validate_filled($value): ?string
{
    if (empty($value) && $value !== '0' && $value !== 0) {
        return 'Это поле должно быть заполнено';
    }

    return null;
}

/**
 * Проверяет длину строки.
 *
 * @param string $str Проверяемая строка
 * @param int    $min Минимальное количество символов
 * @param int    $max Максимальное количество символов
 *
 * @return string Строка сообщения об ошибке, если длина строки невалидна
 */
function validate_str_length(string $str, int $min, int $max): ?string
{
    $length = mb_strlen($str);
    if ($length < $min or $length > $max) {
        return "Значение должно быть от $min до $max символов. Сейчас $length";
    }

    return null;
}

/**
 * Проверяет, существует ли проект у пользователся по указанному id.
 *
 * @param mysqli $link       Ресурс соединения с БД
 * @param int    $user_id    Идентификатор пользователя
 * @param int    $project_id Идентификатор проекта
 *
 * @return string Строка сообщения об ошибке, если пользователь не создавал проект с указанным id
 */
function validate_project_id_exists(mysqli $link, int $user_id, int $project_id): ?string
{
    if (!db_is_project_exist($link, $user_id, ['id' => $project_id])) {
        return 'Выберите существующий проект';
    }

    return null;
}

/**
 * Проверяет, существует ли проект у пользователся по указанному названию.
 *
 * @param mysqli $link    Ресурс соединения с БД
 * @param int    $user_id Идентификатор пользователя
 * @param string $title   Название проекта
 *
 * @return string Строка сообщения об ошибке, если пользователь уже создавал проект с указанным названием
 */
function validate_project_title_exists(mysqli $link, int $user_id, string $title): ?string
{
    if (db_is_project_exist($link, $user_id, ['title' => $title])) {
        return 'Проект с таким названием уже существует. Введите новое название';
    }

    return null;
}

/**
 * Проверяет корректность даты и ее формата.
 *
 * @param string $date Проверяемое значение
 *
 * @return string Строка сообщения об ошибке, если дата или ее формат невалидны
 */
function validate_date_format(string $date): ?string
{
    $format_to_check = 'Y-m-d';
    $date_obj = date_create_from_format($format_to_check, $date);

    if (!$date_obj || array_sum(date_get_last_errors()) !== 0) {
        return 'Дата некорректна. Укажите дату в формате «ГГГГ-ММ-ДД»';
    }

    return null;
}

/**
 * Проверяет, является ли дата прошедшей.
 *
 * @param string $date Проверяемое значение
 *
 * @return string Строка сообщения об ошибке, если дата является прошедшей
 */
function validate_date_in_past(string $date): ?string
{
    $date = date_create($date);
    $today = date_create('today');
    if ($date < $today) {
        return 'Дата должна быть больше или равна текущей';
    }

    return null;
}

/**
 * Проверяет размер загружаемого файла.
 *
 * @param array $file          Массив данных файла из глобального массива $_POST
 * @param int   $max_file_size Максимальный размер файла, МБ
 *
 * @return string Строка сообщения об ошибке, если размер файла превышает допустимый
 */
function validate_file_size(array $file, int $max_file_size): ?string
{
    if ($file['size'] > $max_file_size * 1024 * 1024) {
        return "Размер файла больше допустимого. Максимальный размер - $max_file_size МБ";
    }

    return null;
}

/**
 * Проверяет, зарегистрирован ли пользователь с указанным e-mail.
 *
 * @param mysqli $link  Ресурс соединения с БД
 * @param string $email Проверяемый e-mail
 *
 * @return string Строка сообщения об ошибке, если пользователь с указанным e-mail уже зарегистрирован
 */
function validate_email_is_registered(mysqli $link, string $email): ?string
{
    $user = db_get_user($link, ['email' => $email]);
    if (!empty($user)) {
        return 'Пользователь с указанным e-mail уже зарегистрирован';
    }

    return null;
}

/**
 * Проверяет корректность формата адреса электронной почты.
 *
 * @param string $email Проверяемое значение
 *
 * @return string Строка сообщения об ошибке, если формат адреса элетронной почты некорректный
 */
function validate_email(string $email): ?string
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? null : 'Некорректный формат адреса электронной почты';
}

/**
 * Проверяет корректность идентификатора временной зоны.
 *
 * @param string $timezone_id Идентификатор временной зоны
 * @param array  $timezones   Ассоциативный массив доступных временных зон, ключом в котором является идентификатор зоны
 *
 * @return string
 */
function validate_timezone_id(string $timezone_id, array $timezones): ?string
{
    if (!key_exists($timezone_id, $timezones)) {
        return 'Выберите часовой пояс из списка';
    }

    return null;
}
