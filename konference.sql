-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Sob 12. led 2019, 16:01
-- Verze serveru: 10.1.37-MariaDB
-- Verze PHP: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `konference`
--

CREATE DATABASE IF NOT EXISTS `konference` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;
USE `konference`;

-- --------------------------------------------------------

--
-- Struktura tabulky `articles`
--

CREATE TABLE `articles` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `abstract` text COLLATE utf8_czech_ci NOT NULL,
  `file` varchar(200) CHARACTER SET utf8 NOT NULL,
  `state` tinyint(4) NOT NULL,
  `rating` double NOT NULL,
  `review1_id` int(11) UNSIGNED DEFAULT NULL,
  `review2_id` int(11) UNSIGNED DEFAULT NULL,
  `review3_id` int(11) UNSIGNED DEFAULT NULL,
  `author_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `articles`
--

INSERT INTO `articles` (`id`, `name`, `abstract`, `file`, `state`, `rating`, `review1_id`, `review2_id`, `review3_id`, `author_id`) VALUES
(5, 'ccc', '1', '776f529decafb7854c8c.pdf', 2, 2.67, 8, 9, 10, 1),
(7, 'Článek 5', 'Abs 5', '71b677556fcdd1cc8062.pdf', 0, 0, 11, 14, 16, 2),
(8, 'Článek 6', 'Abs 6', '41ae8294f55b6bb2b3b7.pdf', 0, 0, 12, 15, NULL, 2),
(10, 'dsadsadsa', 'fdgfdgdf', '2b3e955c4d6aa031672b.pdf', 1, 0, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `article_reviews`
--

CREATE TABLE `article_reviews` (
  `id` int(11) UNSIGNED NOT NULL,
  `article_id` int(11) UNSIGNED NOT NULL,
  `reviewer_id` int(10) UNSIGNED NOT NULL,
  `criteria1` int(11) NOT NULL,
  `criteria2` int(11) NOT NULL,
  `criteria3` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `article_reviews`
--

INSERT INTO `article_reviews` (`id`, `article_id`, `reviewer_id`, `criteria1`, `criteria2`, `criteria3`) VALUES
(8, 5, 3, 1, 2, 3),
(9, 5, 5, 2, 3, 4),
(10, 5, 6, 5, 3, 1),
(11, 7, 3, 1, 2, 3),
(12, 8, 3, 2, 3, 4),
(14, 7, 5, 1, 2, 1),
(15, 8, 5, 3, 2, 3),
(16, 7, 6, 2, 3, 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `review_requests`
--

CREATE TABLE `review_requests` (
  `id` int(11) UNSIGNED NOT NULL,
  `article_id` int(10) UNSIGNED NOT NULL,
  `reviewer_id` int(10) UNSIGNED NOT NULL,
  `review_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 NOT NULL,
  `password` varchar(60) CHARACTER SET utf8 NOT NULL,
  `role` tinyint(4) NOT NULL,
  `name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `surname` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `name`, `surname`, `is_blocked`) VALUES
(1, 'autor1@test.cz', '$2y$10$uWqzKC6GWs4ZEoANVjSl4eLh0oYs4pVvmLBrj.3dX69whz99NBTkG', 0, 'Tomáš', 'Autor', 0),
(2, 'autor2@test.cz', '$2y$10$uWqzKC6GWs4ZEoANVjSl4eLh0oYs4pVvmLBrj.3dX69whz99NBTkG', 0, 'Milan', 'Autor', 0),
(3, 'recenzent@test.cz', '$2y$10$uWqzKC6GWs4ZEoANVjSl4eLh0oYs4pVvmLBrj.3dX69whz99NBTkG', 1, 'Pavel', 'Recenzent', 0),
(4, 'admin@test.cz', '$2y$10$uWqzKC6GWs4ZEoANVjSl4eLh0oYs4pVvmLBrj.3dX69whz99NBTkG', 2, 'Petr', 'Admin', 0),
(5, 'recenzent2@test.cz', '$2y$10$uWqzKC6GWs4ZEoANVjSl4eLh0oYs4pVvmLBrj.3dX69whz99NBTkG', 1, 'Martin', 'Recenzent', 0),
(6, 'recenzent3@test.cz', '$2y$10$uWqzKC6GWs4ZEoANVjSl4eLh0oYs4pVvmLBrj.3dX69whz99NBTkG', 1, 'Martin', 'Dub', 0);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD KEY `fk_articles_1_idx` (`author_id`),
  ADD KEY `fk_articles_2_idx` (`review1_id`),
  ADD KEY `fk_articles_3_idx` (`review2_id`),
  ADD KEY `fk_articles_4_idx` (`review3_id`);

--
-- Klíče pro tabulku `article_reviews`
--
ALTER TABLE `article_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD KEY `fk_article_reviews_1_idx` (`article_id`),
  ADD KEY `fk_article_reviews_2_idx` (`reviewer_id`);

--
-- Klíče pro tabulku `review_requests`
--
ALTER TABLE `review_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_review_requests_1_idx` (`article_id`),
  ADD KEY `fk_review_requests_2_idx` (`reviewer_id`);

--
-- Klíče pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pro tabulku `article_reviews`
--
ALTER TABLE `article_reviews`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pro tabulku `review_requests`
--
ALTER TABLE `review_requests`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_articles_2_idx` FOREIGN KEY (`review1_id`) REFERENCES `article_reviews` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_articles_3_idx` FOREIGN KEY (`review2_id`) REFERENCES `article_reviews` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_articles_4_idx` FOREIGN KEY (`review3_id`) REFERENCES `article_reviews` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `article_reviews`
--
ALTER TABLE `article_reviews`
  ADD CONSTRAINT `fk_article_reviews_1_idx` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_article_reviews_2_idx` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `review_requests`
--
ALTER TABLE `review_requests`
  ADD CONSTRAINT `fk_review_requests_1_idx` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_review_requests_2_idx` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
