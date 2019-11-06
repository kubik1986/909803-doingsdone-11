-- Добавляет записи в таблицу пользователей
INSERT INTO users (name, password, email, reg_date) VALUES
  ('John Doe',
    '$2y$10$GU4aYHiHdLwsEUkp2v715ekW/n43w9iaby0q6xq04HNCZnLO7n7fy',
    'john_doe@gmail.com',
    '2019-10-05 15:05:33'),
  ('Jane Smith',
    '$2y$10$AykbIgNv6uw10t3Ns68Bae6RkqcPdVSoyNUTVByOIJPRhiDRnSRku',
    'jane-smith@yahoo.com',
    '2019-10-28 10:42:17');

-- Добавляет записи в таблицу проектов
INSERT INTO projects (title, author_id) VALUES
  ('Входящие', 1),
  ('Учеба', 1),
  ('Работа', 1),
  ('Домашние дела', 1),
  ('Авто', 1),
  ('Работа', 2),
  ('Покупки', 2),
  ('Встречи', 2),
  ('Домашние дела', 2);

-- Добавляет записи в таблицу задач
INSERT INTO tasks (title, adding_date, deadline, is_completed, file_link, file_name, project_id, author_id) VALUES
  ('Собеседование в IT компании',
    '2019-11-01 09:11:42',
    '2019-11-04',
    0,
    NULL,
    NULL,
    3,
    1),
  ('Выполнить тестовое задание',
    '2019-11-01 09:13:11',
    '2019-12-25',
    0,
    'user-1-fgfgfjjh65ggf.pdf',
    'Задание.pdf',
    3,
    1),
  ('Сделать задание первого раздела',
    '2019-11-01 09:14:35',
    '2019-11-03',
    1,
    NULL,
    NULL,
    2,
    1),
  ('Встреча с другом',
    '2019-11-01 09:15:03',
    '2019-11-05',
    0,
    NULL,
    NULL,
    1,
    1),
  ('Купить корм для кота',
    '2019-11-01 09:15:56',
    NULL,
    0,
    NULL,
    NULL,
    4,
    1),
  ('Заказать пиццу',
  '2019-11-01 09:25:26',
  NULL,
  0,
  NULL,
  NULL,
  4,
  1),
  ('Подготовить очет о продажах',
    '2019-11-02 21:02:17',
    '2019-11-09',
    0,
    NULL,
    NULL,
    6,
    2),
  ('Купить детям мороженое',
    '2019-11-02 21:02:17',
    '2019-11-03',
    1,
    NULL,
    NULL,
    7,
    2),
  ('Купить путевки на следующий год',
    '2019-11-02 21:08:44',
    '2019-12-01',
    0,
    NULL,
    NULL,
    7,
    2),
  ('Отмыть плиту',
    '2019-11-02 21:09:32',
    '2019-11-06',
    0,
    NULL,
    NULL,
    9,
    2);

-- Получает список всех проектов одного пользователя
SELECT id, title
  FROM projects
  WHERE author_id = 1
  ORDER BY title;

-- Получает список всех проектов одного пользователя с подсчетом количества задач
SELECT p.id, p.title, COUNT(t.id) AS tasks_count
  FROM projects p
  LEFT JOIN tasks t ON t.project_id = p.id
  WHERE p.author_id = 1
  GROUP BY p.id
  ORDER BY p.title;

-- Получает список всех задач для одного проекта
SELECT id, title, deadline, is_completed, file_link, file_name, project_id
  FROM tasks
  WHERE project_id = 1
  ORDER BY deadline IS NULL, deadline ASC;

-- Получает список всех задач для одного пользователя
SELECT id, title, deadline, is_completed, file_link, file_name, project_id
  FROM tasks
  WHERE author_id = 1
  ORDER BY deadline IS NULL, deadline ASC;

-- Инвертирует статус выполнения задачи
UPDATE tasks
  SET is_completed = NOT is_completed
  WHERE id = 1;

-- Обновляет название задачи по ее идентификатору
UPDATE tasks
  SET title = 'Собеседование в Google o_O'
  WHERE id = 1;
