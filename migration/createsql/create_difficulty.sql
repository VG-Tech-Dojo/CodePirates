CREATE  TABLE IF NOT EXISTS `difficulty` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `content` TEXT NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `updated_at` DATETIME NULL ,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;
