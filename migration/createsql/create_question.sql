CREATE  TABLE IF NOT EXISTS `question` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(50) NOT NULL ,
  `content` TEXT NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `updated_at` DATETIME NULL ,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;
