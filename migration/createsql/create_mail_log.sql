CREATE TABLE IF NOT EXISTS `mail_log` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `u_id` INT NOT NULL,
  `email_address` TEXT NOT NULL
  `subject` TEXT NOT NULL
  `message` TEXT NOT NULL
  `created_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;
