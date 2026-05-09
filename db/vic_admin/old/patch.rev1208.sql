-- phpMyAdmin SQL Dump
-- version 3.3.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 03, 2010 at 11:16 AM
-- Server version: 5.1.47
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `vic_admin`
--

--
-- Dumping data for table `sekce`
--

INSERT INTO `sekce` (`sekce_id`, `parent_id`, `nazev`, `zobrazeno`, `poradi`, `controller`, `action`) VALUES
(163, 0, 'Branch detail', 1, 0, 'branch-contract', 'get-new-form'),
(203, 0, '', 1, 0, 'branch-contract', 'cancel-contract');

INSERT INTO  `vic_admin`.`role_resource_privilege` (
`id` ,
`role_id` ,
`section_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL ,  '2',  '203',  'read',  '1'
), (
NULL ,  '2',  '203',  'write',  '1'
), (
NULL ,  '2',  '203',  'delete',  '1'
);

INSERT INTO  `vic_admin`.`role_resource_privilege` (
`id` ,
`role_id` ,
`section_id` ,
`privilege_name` ,
`privilege_set`
)
VALUES (
NULL ,  '2',  '163',  'read',  '1'
), (
NULL ,  '2',  '163',  'write',  '1'
), (
NULL ,  '2',  '163',  'delete',  '1');
