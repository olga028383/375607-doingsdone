/*Выбирает список всех проектов*/
SELECT * FROM `projects`;
/*Выбрать все задачи для определенного проекта*/
SELECT * FROM `tasks` WHERE project_id = 1;
/*Пометить задачу как выполненную*/
UPDATE `tasks` SET dt_complete = now() WHERE id = 1;
/*Запрос добавляет новый проект, но здесь неправильно здесь нужно доп вставить id  пользователя найленного по его email, наверно*/
INSERT INTO projects(name,user_id) VALUES ('Название',1);
/*Вставить новую задачу*/
INSERT INTO tasks(project_id, dt_created, name) VALUES (1,'07.05.2017', 'Какая-то новая задача');
/*Получить все задачи для завтрашнего дня*/
SELECT * FROM `tasks` WHERE dt_deadline = CURDATE()+1;
/*обновить название задачи по её идентификатору*/
UPDATE tasks SET name = 'Новое значение' WHERE id = 1; 
