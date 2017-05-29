/*Таблица пользователь*/
INSERT INTO user(registered, email, name, password) VALUES (CURDATE(), "ignat.v@gmail.com", "Игнат", "$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka");
INSERT INTO user(registered,email,name,password) VALUES (CURDATE(), "kitty_93@li.ru", "Леночка", "$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa");
INSERT INTO user(registered,email,name,password) VALUES (CURDATE(), "warrior07@mail.ru", "Руслан", "$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW");
/*Таблица проекты*/
INSERT INTO projects(name,user_id) VALUES ('Входящие',1);
INSERT INTO projects(name,user_id) VALUES ('Учеба',1);
INSERT INTO projects(name,user_id) VALUES ('Работа',1);
INSERT INTO projects(name,user_id) VALUES ('Домашние дела',1);
INSERT INTO projects(name,user_id) VALUES ('Авто',1);
/*Таблица задачи*/
INSERT INTO tasks(user_id, project_id, created, deadline, name) VALUES (1, 3, CURDATE(), CURDATE()+ INTERVAL 1 DAY, 'Собеседование в IT компании');
INSERT INTO tasks(user_id, project_id, created, deadline, name) VALUES (2, 3, CURDATE(), CURDATE()+ INTERVAL 1 MONTH, 'Выполнить тестовое задание');
INSERT INTO tasks(user_id, project_id, created, deadline, name) VALUES (3, 2, CURDATE(), CURDATE()+ INTERVAL 10 DAY, 'Сделать задание первого раздела');
INSERT INTO tasks(user_id, project_id, created, deadline, name) VALUES (1, 1, CURDATE(), CURDATE()+ INTERVAL 20 DAY, 'Встреча с другом');
INSERT INTO tasks(user_id, project_id, created, deadline, name) VALUES (2, 4, CURDATE(), CURDATE()+ INTERVAL 2 DAY, 'Купить корм для кота');
INSERT INTO tasks(user_id, project_id, created, deadline, name) VALUES (3, 4, CURDATE(), CURDATE()+ INTERVAL 1 DAY, 'Заказать пиццу');