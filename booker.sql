-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июл 22 2019 г., 01:26
-- Версия сервера: 10.1.37-MariaDB
-- Версия PHP: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `booker`
--

-- --------------------------------------------------------

--
-- Структура таблицы `booker_events`
--

CREATE TABLE `booker_events` (
  `id` int(12) NOT NULL,
  `is_recurring` tinyint(1) NOT NULL,
  `idrec` int(12) DEFAULT NULL,
  `description` varchar(200) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idroom` int(12) NOT NULL,
  `iduser` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `booker_rooms`
--

CREATE TABLE `booker_rooms` (
  `id` int(12) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `booker_rooms`
--

INSERT INTO `booker_rooms` (`id`, `name`) VALUES
(1, 'main boardroom'),
(2, 'meeting room'),
(3, 'small meeting room');

-- --------------------------------------------------------

--
-- Структура таблицы `booker_users`
--

CREATE TABLE `booker_users` (
  `id` int(12) NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `token` varchar(100) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `booker_users`
--

INSERT INTO `booker_users` (`id`, `username`, `password`, `email`, `token`, `is_admin`, `is_active`) VALUES
(1, 'admin', '123', 'admin@local.com', 'b4559be2cf0bd6a979245c86da8182d0', 1, 1),
(2, 'user1', '123', 'user1@local.com', 'f03a1a48866b7dab07e934d071693522', 0, 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `booker_events`
--
ALTER TABLE `booker_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `iduser` (`iduser`),
  ADD KEY `idroom` (`idroom`);

--
-- Индексы таблицы `booker_rooms`
--
ALTER TABLE `booker_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `booker_users`
--
ALTER TABLE `booker_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `booker_events`
--
ALTER TABLE `booker_events`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT для таблицы `booker_rooms`
--
ALTER TABLE `booker_rooms`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `booker_users`
--
ALTER TABLE `booker_users`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `booker_events`
--
ALTER TABLE `booker_events`
  ADD CONSTRAINT `booker_events_ibfk_1` FOREIGN KEY (`iduser`) REFERENCES `booker_users` (`id`),
  ADD CONSTRAINT `booker_events_ibfk_2` FOREIGN KEY (`idroom`) REFERENCES `booker_rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
