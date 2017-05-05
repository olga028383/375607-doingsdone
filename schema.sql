CREATE DATABASE `thingsarefine`;
USE `thingsarefine`;
CREATE TABLE `projects` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`name` CHAR(255) NOT NULL,
`user_id` INT NOT NULL
);
CREATE TABLE `tasks` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`user_id` INT NOT NULL,
`project_id` INT NOT NULL,
`dt_created` DATETIME NOT NULL,
`dt_complete` DATETIME,
`dt_deadline` DATETIME,
`name` CHAR(255) NOT NULL,
`file` CHAR(255)
);
CREATE TABLE `user` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`dt_registered` DATETIME NOT NULL,
`email`CHAR(155) NOT NULL,
`name` CHAR(155) NOT NULL,
`avatar` CHAR(100),
`password` CHAR(32) NOT NULL,
`contants` CHAR(255)
);
CREATE UNIQUE INDEX user_name ON projects(user_id, name);
CREATE UNIQUE INDEX email ON user(email);
CREATE INDEX user ON projects(user_id);
CREATE INDEX user ON tasks(user_id);
CREATE INDEX project ON tasks(project_id);