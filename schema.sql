CREATE DATABASE `thingsarefine`;
USE `thingsarefine`;
CREATE TABLE `projects` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`name` CHAR(255)
);
CREATE TABLE `tasks` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`user_id` INT,
`project_id` INT,
`dt_add` DATETIME,
`dt_delete` DATETIME,
`name` CHAR(255),
`file` CHAR(255),
`dt_life` DATETIME
);
CREATE TABLE `user` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`task_id` INT,
`project_id` INT,
`dt_register` DATETIME,
`email`CHAR(155),
`name` CHAR(155),
`file` CHAR(100),
`password` CHAR(32),
`contants` CHAR(255)
);
CREATE TABLE `role` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`anonymous` TINYINT(1)
);
CREATE UNIQUE INDEX nameproject ON project(name);
CREATE UNIQUE INDEX email ON user(email);
CREATE INDEX name ON tasks(name);
CREATE INDEX dt_add ON tasks(dt_add);
CREATE INDEX dt_delete ON tasks(dt_delete);
CREATE INDEX dt_life ON tasks(dt_life);