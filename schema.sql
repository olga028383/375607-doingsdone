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
CREATE UNIQUE INDEX nameproject ON projects(name);
CREATE UNIQUE INDEX email ON user(email);
CREATE INDEX name ON tasks(name);
CREATE INDEX dt_created ON tasks(dt_created);
CREATE INDEX dt_complete ON tasks(dt_complete);
CREATE INDEX dt_deadline ON tasks(dt_deadline);