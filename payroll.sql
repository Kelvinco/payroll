-- Adminer Mende MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `payroll`;
CREATE DATABASE `payroll` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `payroll`;

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(148) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `admin` (`id`, `email`, `username`, `password`) VALUES
  (1,	'your_email@host.com',	'supervisor',	'ba42f32adc08f4a6993f635a47fa323e6b9a7621438d445ae310aaf80337e0c392d8e2835cbe54d11c84faf069b51d4a353a40a4ca786ef638dd40b70ae3baed');

CREATE TABLE `allowance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `allowance` (`id`, `name`) VALUES
  (8,	'House Allowance');

CREATE TABLE `company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `kra_pin` varchar(11) NOT NULL,
  `box_address` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `company` (`id`, `name`, `kra_pin`, `box_address`) VALUES
  (1,	'Makmesh Payroll (Kenya)',	'A000000000Z',	'P.O Box 000000 00100 - Nairobi');

CREATE TABLE `deduction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `value` double DEFAULT NULL,
  `auto` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `deduction` (`id`, `name`, `value`, `auto`) VALUES
  (1,	'Monthly Relief',	1162,	0),
  (2,	'N.S.S.F',	1080,	0),
  (3,	'Tax',	0,	1),
  (4,	'Insurance Relief',	NULL,	0),
  (5,	'N.H.I.F',	NULL,	1),
  (6,	'Pension Plan',	0.05,	0),
  (7,	'Standing Order',	NULL,	0),
  (8,	'Advances',	NULL,	0),
  (9,	'Loan Repayment',	NULL,	0),
  (10,	'Misc Ded/Ref',	NULL,	0),
  (11,	'Pension Refund',	NULL,	0),
  (12,	'HELB',	NULL,	0);

CREATE TABLE `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(20) NOT NULL,
  `mname` varchar(20) NOT NULL,
  `lname` varchar(20) NOT NULL,
  `pin` varchar(20) NOT NULL,
  `national_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `nssf_no` int(11) DEFAULT NULL,
  `nhif_no` int(11) DEFAULT NULL,
  `account_no` int(11) NOT NULL,
  `basic_pay` double NOT NULL,
  `bank` varchar(50) NOT NULL,
  `branch` varchar(50) NOT NULL,
  `code` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_no` (`account_no`),
  UNIQUE KEY `email` (`email`),
  KEY `national_id` (`national_id`),
  KEY `pin` (`pin`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `employee` (`id`, `fname`, `mname`, `lname`, `pin`, `national_id`, `email`, `nssf_no`, `nhif_no`, `account_no`, `basic_pay`, `bank`, `branch`, `code`) VALUES
  (2,	'Nairobi',	'C',	'D',	'A000000058F',	345678,	'a@a.com',	0,	0,	78909890,	671000,	'KCB',	'Kipande',	8787),
  (3,	'Narok',	'B',	'C',	'A000006829Y',	567890,	'b@a.com',	0,	0,	98767898,	351000,	'Family',	'Kipande',	787),
  (4,	'Nakuru',	'D',	'A',	'A003000092L',	876789,	'c@a.com',	0,	0,	7890098,	318000,	'Unaitas',	'Kimathi',	567),
  (5,	'Kisii',	'U',	'J',	'A005000003W',	456789,	'd@a.com',	0,	0,	3456789,	515000,	'COOP',	'Thika',	346),
  (6,	'Kisumu',	'V',	'O',	'A000000030L',	98765,	'e@a.com',	0,	0,	76543456,	415000,	'BOA',	'Kenyatta',	2345),
  (7,	'Busia',	'R',	'P',	'A000000009C',	4567890,	'f@a.com',	0,	0,	5678987,	501000,	'BOB',	'Kipande',	432);

CREATE TABLE `employee_allowance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `allowance` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `employee_allowance` (`id`, `employee_id`, `amount`, `month`, `year`, `allowance`) VALUES
  (11,	2,	24000,	1,	2016,	'House Allowance'),
  (17,	3,	28000,	1,	2016,	'House Allowance');

CREATE TABLE `nhif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `min` int(11) NOT NULL,
  `max` int(11) DEFAULT NULL,
  `deduction` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `nhif` (`id`, `min`, `max`, `deduction`) VALUES
  (1,	1000,	5999,	150),
  (2,	6000,	7999,	300),
  (3,	8000,	11999,	400),
  (4,	12000,	14999,	500),
  (5,	15000,	19999,	600),
  (6,	20000,	24999,	750),
  (7,	25000,	29999,	850),
  (8,	30000,	34999,	900),
  (9,	35000,	39999,	950),
  (10,	40000,	44999,	1000),
  (11,	45000,	49999,	1100),
  (12,	50000,	59999,	1200),
  (13,	60000,	69999,	1300),
  (14,	70000,	79999,	1400),
  (15,	80000,	89999,	1500),
  (16,	90000,	99999,	1600),
  (17,	100000,	0,	1700);

CREATE TABLE `overtime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `value` double NOT NULL,
  `gross_pay` double NOT NULL,
  `net_tax` double NOT NULL,
  `actual_tax` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `pay` (`id`, `employee_id`, `month`, `year`, `value`, `gross_pay`, `net_tax`, `actual_tax`) VALUES
  (1,	1,	1,	2016,	70338,	101000,	24232,	25394),
  (2,	2,	1,	2016,	54038,	91000,	21232,	22394),
  (3,	3,	1,	2016,	24360,	35000,	4627,	5789),
  (4,	4,	1,	2016,	28693,	38000,	5377,	6539),
  (5,	5,	1,	2016,	34538,	55000,	10432,	11594),
  (6,	6,	1,	2016,	33138,	45000,	7432,	8594),
  (7,	7,	1,	2016,	36288,	50000,	8932,	10094);

CREATE TABLE `paye` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `min` int(11) NOT NULL,
  `max` int(11) DEFAULT NULL,
  `rate` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `paye` (`id`, `min`, `max`, `rate`) VALUES
  (1,	1,	10164,	0.1),
  (2,	10165,	19740,	0.15),
  (3,	19741,	29316,	0.2),
  (4,	29317,	38892,	0.25),
  (5,	38893,	0,	0.3);

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `item` varchar(50) NOT NULL,
  `value` double NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `payroll` (`id`, `employee_id`, `item`, `value`, `month`, `year`) VALUES
  (1,	2,	'Loan Repayment',	10000,	1,	2016),
  (2,	3,	'Misc Ded/Ref',	2233,	1,	2016),
  (3,	5,	'Loan Repayment',	5000,	1,	2016);

ALTER TABLE `employee` ADD `exclude` INT(11) NOT NULL DEFAULT '0';
-- 2016-02-11 12:14:31