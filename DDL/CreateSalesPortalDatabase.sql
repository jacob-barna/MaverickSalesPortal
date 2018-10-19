 CREATE TABLE `User` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(45) NOT NULL,
  `Password` char(35) NOT NULL,
  `FirstName` varchar(45) NOT NULL,
  `LastName` varchar(45) NOT NULL,
  `Supervisor` tinyint(1) NOT NULL DEFAULT '0',
  `Approved` tinyint(1) NOT NULL DEFAULT '0',
  `Active` tinyint(1) NOT NULL DEFAULT '0',
  `LinkId` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--active for this table is a "soft delete" we won't 
-- show inactive customers
 CREATE TABLE `Customer` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT '1', 
  `ShipAddress` int(10) unsigned NOT NULL,
  `BillAddress` int(10) unsigned NOT NULL,
  `ContactDetail` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ShipAddress_idx` (`ShipAddress`),
  KEY `FK_BillAddress_idx` (`BillAddress`),
  KEY `FK_ContactDetail_idx` (`ContactDetail`),
  CONSTRAINT `FK_ShipAddress` FOREIGN KEY (`ShipAddress`) REFERENCES `Address` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_BillAddress` FOREIGN KEY (`BillAddress`) REFERENCES `Address` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_ContactDetail` FOREIGN KEY (`ContactDetail`) REFERENCES `ContactDetail` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--for our school project, customer will have only one 
--shipping and one billing address - both can be the sames
CREATE TABLE `Address` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Line1` varchar(128) NOT NULL,
  `Line2` varchar(128) NOT NULL,
  `City` varchar(128) NOT NULL,
  `State` int(10) unsigned NOT NULL,
  `Zip5` char(5) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_State_idx` (`State`),
  CONSTRAINT `FK_State` FOREIGN KEY (`State`) REFERENCES `State` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `State` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(128) NOT NULL,
  `Abbreviation` char(2) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `ContactDetail` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Phone` varchar(128) NOT NULL,
  `Fax` varchar(128) NOT NULL,
  `Email` varchar(128) NOT NULL,
  `WebAddress` varchar(128) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `SalesPortal`.`Item` (
  `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `Price` DECIMAL(8,3) UNSIGNED NOT NULL COMMENT '',
  `Name` VARCHAR(45) NOT NULL COMMENT '',
  PRIMARY KEY (`Id`)  COMMENT '')
ENGINE = InnoDB 
