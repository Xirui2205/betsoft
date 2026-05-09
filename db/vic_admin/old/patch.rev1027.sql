-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Sobota 21. srpna 2010, 14:25
-- Verze MySQL: 5.1.41
-- Verze PHP: 5.3.2-1ubuntu4.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `vic_admin`
--

--
-- Vypisuji data pro tabulku `contract_parameter`
--

INSERT INTO `contract_parameter` (`id`, `name`, `template_id`, `type`, `mandatory`) VALUES
(1, 'vyse-zalohy', 1, 'float', 1),
(2, 'odvod-statu', 1, 'float', 1),
(3, 'zuctovaci-obdobi', 1, 'string', 1),
(5, 'vyse-zalohy', 2, 'float', 1),
(6, 'pausal', 2, 'float', 1),
(7, 'sleva-na-zaloze', 2, 'float', 1),
(8, 'odvod-statu', 2, 'float', 1),
(9, 'vyse-pausalu', 3, 'float', 1),
(11, 'vyse-naberu', 4, 'float', 1);
