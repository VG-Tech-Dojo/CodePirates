CREATE  TABLE IF NOT EXISTS `like` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `u_id` INT NOT NULL,
  `a_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL ,
  `updated_at` DATETIME NULL ,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;
