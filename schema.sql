CREATE DATABASE `thingsarefine`;
USE `thingsarefine`;
CREATE TABLE `projects` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`name` CHAR(255),
`user_id` INT
);
CREATE TABLE `tasks` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`user_id` INT,
`project_id` INT,
`dt_created` DATETIME,
`dt_complete` DATETIME,
`dt_deadline` DATETIME,
`name` CHAR(255),
`file` CHAR(255)
);
CREATE TABLE `user` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`dt_registered` DATETIME,
`email`CHAR(155),
`name` CHAR(155),
`avatar` CHAR(100),
`password` CHAR(32),
`contants` CHAR(255)
);
CREATE TABLE `role` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`anonymous` TINYINT(1)
);
CREATE UNIQUE INDEX nameproject ON projects(name);
CREATE UNIQUE INDEX email ON user(email);
CREATE INDEX name ON tasks(name);
CREATE INDEX dt_created ON tasks(dt_created);
CREATE INDEX dt_complete ON tasks(dt_complete);
CREATE INDEX dt_deadline ON tasks(dt_deadline);