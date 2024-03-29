<?php
/**
 * Устанавливает временную зону для сеанса подключения к БД.
 *
 * @param mysqli $link     Ресурс соединения
 * @param string $timezone Идентификатор временной зоны
 */
function db_set_time_zone(mysqli $link, string $timezone): void
{
    $sql = "SET time_zone = '".$timezone."'";
    $result = mysqli_query($link, $sql);

    if (!$result) {
        exit('Произошла ошибка MySQL. Попробуйте повторить позднее или обратитесь к администратору.');
    }
}

/**
 * Создает подключение к сервру MySQL и возвращает идентификатор подключения.
 *
 * @param array $db Массив с параметрами подключения
 *
 * @return mysqli $link Идентификатор подключения к серверу MySQL
 */
function db_connect(array $db): mysqli
{
    $link = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
    if ($link) {
        db_set_time_zone($link, $db['timezone']);
    }
    if (!$link) {
        exit('Произошла ошибка MySQL. Попробуйте повторить позднее или обратитесь к администратору.');
    }

    return $link;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных.
 *
 * @param mysqli $link Ресурс соединения
 * @param string $sql  SQL запрос с плейсхолдерами вместо значений
 * @param array  $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt(mysqli $link, string $sql, array $data = []): mysqli_stmt
{
    $stmt = mysqli_prepare($link, $sql);
    if ($data) {
        $types = '';
        $stmt_data = [];
        foreach ($data as $value) {
            $type = null;
            if (is_int($value)) {
                $type = 'i';
            } elseif (is_string($value)) {
                $type = 's';
            } elseif (is_double($value)) {
                $type = 'd';
            }
            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }
        $values = array_merge([$stmt, $types], $stmt_data);
        $func = 'mysqli_stmt_bind_param';
        $func(...$values);
    }

    return $stmt;
}

/**
 * Получает записи из БД.
 *
 * @param mysqli $link      Ресурс соединения
 * @param string $sql       SQL запрос с плейсхолдерами вместо значений
 * @param array  $data      Данные для вставки на место плейсхолдеров
 * @param bool   $fetch_one Параметр, определяющий вид результирующего массива: true - возвращается одномерный ассоциативный массив (одна запись из БД), false - возвращается двумерный ассоциативный массив (несколько записей из БД)
 *
 * @return array Массив записей по результату запроса
 */
function db_fetch_data(mysqli $link, string $sql, array $data = [], bool $fetch_one = false): array
{
    $fetch_func = $fetch_one ? 'mysqli_fetch_array' : 'mysqli_fetch_all';
    $result = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        $result = $fetch_func($res, MYSQLI_ASSOC);
    } else {
        exit('Произошла ошибка MySQL. Попробуйте повторить позднее или обратитесь к администратору.');
    }

    return $result;
}

/**
 * Добавляет новую запись в БД.
 *
 * @param mysqli $link Ресурс соединения
 * @param string $sql  SQL запрос с плейсхолдерами вместо значений
 * @param array  $data Данные для вставки на место плейсхолдеров
 *
 * @return int Идентификатор добавленной записи
 */
function db_insert_data(mysqli $link, string $sql, array $data = []): int
{
    $result = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $result = mysqli_insert_id($link);
    } else {
        exit('Произошла ошибка MySQL. Попробуйте повторить позднее или обратитесь к администратору.');
    }

    return $result;
}

/**
 * Обновляет запись в БД.
 *
 * @param mysqli $link Ресурс соединения
 * @param string $sql  SQL запрос с плейсхолдерами вместо значений
 * @param array  $data Данные для вставки на место плейсхолдеров
 *
 * @return bool true - данные успешно оновлены, false - данные не обновлены
 */
function db_update_data(mysqli $link, string $sql, array $data = []): bool
{
    $result = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        exit('Произошла ошибка MySQL. Попробуйте повторить позднее или обратитесь к администратору.');
    }

    return $result;
}

/**
 * Проверяет, существует ли указанный проект (по id или названию) у определенного пользователя.
 *
 * @param mysqli $link    Ресурс соединения
 * @param int    $user_id Идентификатор пользователя
 * @param array  $where   Ассоциативный массив вида ['ключ_поиска' => 'значение_ключа_поиска'], который определяет, по какому полю будет проходить поиск ('id'|'title')
 *
 * @return bool true, если проект существует у пользователя, иначе false
 */
function db_is_project_exist(mysqli $link, int $user_id, array $where): bool
{
    $selector = key($where);
    $data = [current($where)];
    $sql =
        "SELECT *
            FROM projects
            WHERE author_id = $user_id AND $selector = ?";
    $project = db_fetch_data($link, $sql, $data);

    return count($project) !== 0;
}

/**
 * Проверяет, существует ли указанная задача (по id) у определенного пользователя.
 *
 * @param mysqli $link    Ресурс соединения
 * @param int    $user_id Идентификатор пользователя
 * @param array  $task_id Идентификатор задачи
 *
 * @return bool true, если задача существует у пользователя, иначе false
 */
function db_is_task_exist(mysqli $link, int $user_id, int $task_id): bool
{
    $sql =
        "SELECT *
            FROM tasks
            WHERE id = ? AND author_id = $user_id";
    $project = db_fetch_data($link, $sql, [$task_id]);

    return count($project) !== 0;
}

/**
 * Получает список проектов указанного пользователя с подсчетом количества задач в каждом проекте c учетом фильтрации задач по срочности и статуса выполнения.
 *
 * @param mysqli $link                 Ресурс соединения
 * @param int    $user_id              Идентификатор пользователя
 * @param string $task_filter          Имя фильтра задач
 * @param int    $show_completed_tasks Фильтр статуса выполнения задачи: 1 - подсчитывать в том числе выполненные задачи, 0 - не подсчитывать выполненные задачи
 *
 * @return array Массив проектов пользователя
 */
function db_get_projects(mysqli $link, int $user_id, string $task_filter = 'all', int $show_completed_tasks = 0): array
{
    $task_completed_select = $show_completed_tasks ? '' : 'AND NOT t.is_completed';
    $task_count_select = '';
    switch ($task_filter) {
        case 'today':
            $task_count_select = "CASE WHEN (t.deadline = CURDATE() $task_completed_select) THEN 1 ELSE NULL END";
            break;
        case 'tomorrow':
            $task_count_select = "CASE WHEN (t.deadline = ADDDATE(CURDATE(), INTERVAL 1 DAY) $task_completed_select) THEN 1 ELSE NULL END";
            break;
        case 'overdue':
            $task_count_select = 'CASE WHEN (t.deadline < CURDATE() AND NOT t.is_completed) THEN 1 ELSE NULL END';
            break;
        default:
            $task_count_select = "CASE WHEN (t.id $task_completed_select) THEN 1 ELSE NULL END";
    }
    $sql =
        "SELECT p.id, p.title, COUNT($task_count_select) AS tasks_count
            FROM projects p
            LEFT JOIN tasks t ON t.project_id = p.id
            WHERE p.author_id = ?
            GROUP BY p.id
            ORDER BY p.title";

    return db_fetch_data($link, $sql, [$user_id]);
}

/**
 * Получает список задач указанного пользователя с фильтрацией по проекту и срочности.
 *
 * @param mysqli $link                 Ресурс соединения
 * @param int    $user_id              Идентификатор пользователя
 * @param int    $project_id           Идентификатор проекта
 * @param int    $filter               Имя фильтра срочности задачи
 * @param int    $show_completed_tasks Фильтр статуса выполнения задачи: 1 - показывать выполненные задачи, 0 - не показывать выполненные задачи
 * @param string $search               Строка поискового запроса
 *
 * @return array Массив задач
 */
function db_get_tasks(mysqli $link, int $user_id, ?int $project_id = null, string $filter = 'all', int $show_completed_tasks = 0, string $search = ''): array
{
    $data = [$user_id];
    $project_select = '';
    $search_select = '';
    if (!empty($search)) {
        $search_select = 'AND MATCH (title) AGAINST (? IN BOOLEAN MODE)';
        $data[] = $search;
    }
    if (!empty($project_id)) {
        $project_select = 'AND project_id = ?';
        $data[] = $project_id;
    }
    $filter_select = '';
    switch ($filter) {
        case 'today':
            $filter_select = 'AND deadline = CURDATE()';
            break;
        case 'tomorrow':
            $filter_select = 'AND deadline = ADDDATE(CURDATE(), INTERVAL 1 DAY)';
            break;
        case 'overdue':
            $filter_select = 'AND (deadline < CURDATE() AND NOT is_completed)';
            break;
        default:
            $filter_select = '';
    }
    $is_completed_select = $show_completed_tasks ? '' : 'AND NOT is_completed';
    $sql =
        "SELECT id, title, deadline, is_completed, file_link, file_name, project_id
            FROM tasks
            WHERE author_id = ? $search_select $project_select $filter_select $is_completed_select
            ORDER BY deadline IS NULL, deadline ASC";

    return db_fetch_data($link, $sql, $data);
}

/**
 * Добавляет запись новой задачи в таблицу tasks БД.
 *
 * @param mysqli $link Ресурс соединения
 * @param array  $data Массив данных новой задачи для вставки в запрос
 *
 * @return int Идентификатор добавленной записи
 */
function db_add_task(mysqli $link, array $data): int
{
    $stmt_data = [
        $data['name'],
        $data['author_id'],
        $data['project'],
    ];

    $deadline_field = empty($data['date']) ? '' : ',deadline';
    $deadline_placeholder = empty($data['date']) ? '' : ',?';
    $file_link_field = empty($data['file_link']) ? '' : ',file_link';
    $file_link_placeholder = empty($data['file_link']) ? '' : ',?';
    $file_name_field = empty($data['file_name']) ? '' : ',file_name';
    $file_name_placeholder = empty($data['file_name']) ? '' : ',?';

    if (!empty($data['date'])) {
        $stmt_data[] = $data['date'];
    }
    if (!empty($data['file_link'])) {
        $stmt_data[] = $data['file_link'];
    }
    if (!empty($data['file_name'])) {
        $stmt_data[] = $data['file_name'];
    }

    $sql =
        "INSERT INTO tasks (title, author_id, project_id $deadline_field $file_link_field $file_name_field)
            VALUES (?, ?, ? $deadline_placeholder $file_link_placeholder $file_name_placeholder)";

    return db_insert_data($link, $sql, $stmt_data);
}

/**
 * Добавляет запись нового проекта в таблицу projects БД.
 *
 * @param mysqli $link Ресурс соединения
 * @param array  $data Массив данных нового проекта для вставки в запрос
 *
 * @return int Идентификатор добавленной записи
 */
function db_add_project(mysqli $link, array $data): int
{
    $sql =
        'INSERT INTO projects (title, author_id)
            VALUES (?, ?)';

    return db_insert_data($link, $sql, $data);
}

/**
 * Получает данные пользователя (по id или email).
 *
 * @param mysqli $link  Ресурс соединения
 * @param array  $where Ассоциативный массив вида ['ключ_поиска' => 'значение_ключа_поиска'], который определяет, по какому полю будет проходить поиск ('id'|'email')
 *
 * @return array Массив данных пользователя
 */
function db_get_user(mysqli $link, array $where): array
{
    $selector = key($where);
    $data = [current($where)];
    $sql =
        "SELECT *
            FROM users
            WHERE $selector = ?";

    return db_fetch_data($link, $sql, $data, true);
}

/**
 * Добавляет запись данных нового пользователя в таблицу users БД.
 *
 * @param mysqli $link Ресурс соединения
 * @param mixed  $data Массив данных нового пользователя для вставки в запрос
 *
 * @return int Идентификатор добавленной записи
 */
function db_add_user(mysqli $link, array $data): int
{
    $sql =
        'INSERT INTO users (email, password, name, timezone)
            VALUES (?, ?, ?, ?)';

    return db_insert_data($link, $sql, $data);
}

/**
 * Обновляет данные пользователя в БД.
 *
 * @param mysqli $link    Ресурс соединения
 * @param int    $user_id Идентификатор пользователя
 * @param array  $data    Массив данных пользователя
 *
 * @return bool true, если данные успешно обновлены, иначе false
 */
function db_update_user(mysqli $link, int $user_id, array $data): bool
{
    $password_set = '';
    $stmt_data = [];
    $stmt_data['name'] = $data['name'];
    $stmt_data['timezone'] = $data['timezone'];
    if (!empty($data['password'])) {
        $password_set = ', password = ?';
        $stmt_data['password'] = $data['password'];
    }
    $sql =
        "UPDATE users
            SET name = ?, timezone = ? $password_set
            WHERE id = $user_id";

    return db_update_data($link, $sql, $stmt_data);
}

/**
 * Инвертирует статус выполнения задачи.
 *
 * @param mysqli $link    Ресурс соединения
 * @param int    $task_id Идентификатор задачи
 *
 * @return bool true, если статус успешно обновлен, иначе false
 */
function db_invert_task_status(mysqli $link, int $task_id): bool
{
    $sql =
        'UPDATE tasks
            SET is_completed = NOT is_completed
            WHERE id = ?';

    return db_update_data($link, $sql, [$task_id]);
}

/**
 * Обновляет статус выполнения задачи.
 *
 * @param mysqli $link        Ресурс соединения
 * @param int    $task_id     Идентификатор задачи
 * @param int    $task_status Статус выполнения задачи (0 или 1)
 *
 * @return bool true, если статус успешно обновлен, иначе false
 */
function db_update_task_status(mysqli $link, int $task_id, int $task_status): bool
{
    $sql =
        'UPDATE tasks
            SET is_completed = ?
            WHERE id = ?';

    return db_update_data($link, $sql, [$task_status, $task_id]);
}
