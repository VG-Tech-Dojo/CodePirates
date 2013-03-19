CREATE  TABLE IF NOT EXISTS `answer` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `u_id` INT NOT NULL,
  `q_id` INT NOT NULL,
  `content` TEXT NOT NULL ,
  `lang` VARCHAR(255) NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `updated_at` DATETIME NULL ,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;
