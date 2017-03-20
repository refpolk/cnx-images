DELIMITER ;;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";;

CREATE TABLE IF NOT EXISTS `Photos` (
	`ID` 			INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`Photonum` 		VARCHAR(10) DEFAULT NULL,
	`OldPhotonum` 	VARCHAR(10) DEFAULT NULL,
	`Title` 		VARCHAR(115) NOT NULL,
	`Filename` 		VARCHAR(120) NOT NULL,
	`URL` 			VARCHAR(150) DEFAULT NULL,
	`Year` 			VARCHAR(5) DEFAULT NULL,
	`Date` 			VARCHAR(8) DEFAULT NULL,
	`Authors` 		VARCHAR(100) DEFAULT NULL,
	`Place` 		VARCHAR(60) DEFAULT NULL,
	`Caption` 		TEXT,
	`Negscan` 		CHAR(1) DEFAULT NULL,
	`Publishist` 	TEXT,
	`Nix` 			CHAR(1) DEFAULT NULL,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;;

DROP PROCEDURE IF EXISTS update_table;;

CREATE PROCEDURE update_table ()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060, 1061, 1054 BEGIN END;
	
	ALTER TABLE `Photos` CHANGE  `Authors` `Author` VARCHAR(100) DEFAULT NULL;
    
	ALTER TABLE `Photos` ADD `Note` TEXT;
	
	ALTER TABLE `Photos` ADD UNIQUE `ix_unique` (`Title`, `Author`, `Filename`, `URL`);
	
	ALTER TABLE `Photos` ADD FULLTEXT `ix_fulltext` (`Title`, `Author`, `Filename`, `Note`, `Caption`, `Place`);
	
	ALTER TABLE `Photos` MODIFY `Title` VARCHAR(200) NOT NULL;
	
	ALTER TABLE `Photos` MODIFY `Filename` VARCHAR(120) DEFAULT NULL;
	
	ALTER TABLE `Photos` MODIFY `Caption` VARCHAR(1000) DEFAULT NULL;
	
	ALTER TABLE `Photos` MODIFY `Note` VARCHAR(1000) DEFAULT NULL;
	
	ALTER TABLE `Photos` MODIFY `Publishist` VARCHAR(200) DEFAULT NULL;
END;;

CALL update_table();;
	
DELIMITER ;