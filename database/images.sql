DELIMITER ;;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";;

CREATE TABLE IF NOT EXISTS `Images` (
	`ID` 			INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`Title` 		VARCHAR(115) NOT NULL,
	`Filename` 		VARCHAR(120) NOT NULL,
	`URL` 			VARCHAR(150) DEFAULT NULL,
	`Author` 		VARCHAR(100) DEFAULT NULL,
	`Year` 			VARCHAR(10) DEFAULT NULL,
	`Source` 		VARCHAR(50) DEFAULT NULL,
	`ELibrary` 		CHAR(1) DEFAULT NULL,
	`Caption` 		TEXT,
	`Publishist` 	VARCHAR(100) DEFAULT NULL,
	`Copyright` 	TEXT,
	`Marked` 		CHAR(2) DEFAULT NULL,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;;

DROP PROCEDURE IF EXISTS update_table;;
	
CREATE PROCEDURE update_table ()
BEGIN
    DECLARE CONTINUE HANDLER FOR 1060, 1061 BEGIN END;
    
	ALTER TABLE `Images` ADD `Note` text;
	
	ALTER TABLE `Images` ADD UNIQUE `ix_unique` (`Title`, `Author`, `Filename`, `URL`);
	
	ALTER TABLE `Images` ADD FULLTEXT `ix_fulltext` (`Title`, `Author`, `Filename`, `Note`, `Caption`);
	
	ALTER TABLE `Images` MODIFY `Title` VARCHAR(200) NOT NULL;
	
	ALTER TABLE `Images` MODIFY `Filename` VARCHAR(120) DEFAULT NULL;
	
	ALTER TABLE `Images` MODIFY `Caption` VARCHAR(1000) DEFAULT NULL;
	
	ALTER TABLE `Images` MODIFY `Note` VARCHAR(1000) DEFAULT NULL;	
	
	ALTER TABLE `Images` MODIFY `Copyright` VARCHAR(1000) DEFAULT NULL;	
END;;

CALL update_table();;
	
DELIMITER ;