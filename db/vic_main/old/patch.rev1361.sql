-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Úterý 14. září 2010, 10:21
-- Verze MySQL: 5.1.41
-- Verze PHP: 5.3.2-1ubuntu4.2

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `vic_main`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `financial_transaction_type`
--

DROP TABLE IF EXISTS `financial_transaction_type`;
CREATE TABLE IF NOT EXISTS `financial_transaction_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `note` text NOT NULL,
  `from` varchar(8) DEFAULT NULL,
  `thru` varchar(8) DEFAULT NULL,
  `to` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Vypisuji data pro tabulku `financial_transaction_type`
--

INSERT INTO `financial_transaction_type` (`id`, `name`, `note`, `from`, `thru`, `to`) VALUES
(1, 'branch.ticket.create-cash', '', '211', NULL, '602'),
(2, 'user.ticket.create-online', '', '324', NULL, '602'),
(3, 'branch.ticket.cancel-cash', '', '602', NULL, '211'),
(4, 'user.ticket.cancel-online', '', '602', NULL, '324'),
(5, 'user.ticket.payout-cash', '', '548', NULL, '379'),
(6, 'user.ticket.payout-online', '', '548', '379', '324'),
(7, 'user.ticket.payout-storno', '', NULL, NULL, NULL),
(8, 'user.deposit.online-card', '', '378', NULL, '324'),
(9, 'user.deposit.branch-cash', '', '211', NULL, '324'),
(10, 'user.deposit.bank-transfer', '', '221', NULL, '324'),
(11, 'user.withdraw.cash-branch', '', '324', NULL, '379'),
(12, 'user.withdraw.bank-transfer', '', '324', NULL, '379'),
(13, 'branch.ticket.create-online', '', NULL, NULL, NULL),
(14, 'branch.ticket.cancel-online', '', NULL, NULL, NULL),
(15, 'user.ticket.create-cache', '', NULL, NULL, NULL),
(16, 'user.ticket.cancel-cash', '', NULL, NULL, NULL),
(17, 'branch.ticket.payout-cash', '', '379', NULL, '211'),
(18, 'branch.ticket.payout-online', '', NULL, NULL, NULL),
(19, 'branch.ticket.payout-storno', 'Storno již proplaceného tiketu. (Lze delat jen online)', NULL, NULL, NULL),
(20, 'branch.user-deposit.cash', '', NULL, NULL, NULL),
(21, 'branch.user-withdraw.cash', '', '379', NULL, '211'),
(22, 'branch.deposit', '', '261', NULL, '211'),
(23, 'branch.withdraw', '', '211', NULL, '261');
SET FOREIGN_KEY_CHECKS=1;
COMMIT;
