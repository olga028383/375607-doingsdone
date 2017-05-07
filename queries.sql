/*Выбирает список всех проектов*/
SELECT * FROM `projects` WHERE user_id = 1;
/*Выбрать все задачи для определенного проекта*/
SELECT * FROM `tasks` WHERE project_id = 1;
/*Пометить задачу как выполненную*/
UPDATE `tasks` SET complete = now() WHERE id = 1;
/*Запрос добавляет новый проект, но здесь неправильно здесь нужно доп вставить id  пользователя найленного по его email, наверно*/
INSERT INTO projects(name,user_id) VALUES ('Название',1);
/*Вставить новую задачу*/
INSERT INTO tasks(user_id, project_id, created, deadline, name) VALUES (1, 1, CURDATE(), CURDATE()+ INTERVAL 1 DAY, 'Какая-то новая задача1');
/*Получить все задачи для завтрашнего дня*/
SELECT * FROM `tasks` WHERE deadline = CURDATE() + INTERVAL 1 DAY;
/*обновить название задачи по её идентификатору*/
UPDATE tasks SET name = 'Новое значение' WHERE id = 1; 
