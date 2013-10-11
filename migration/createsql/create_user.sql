CREATE  TABLE IF NOT EXISTS `user` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(50) NOT NULL ,
  `password` VARCHAR(255) NOT NULL ,
  `salt` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `created_time` DATETIME NOT NULL ,
  `updated_time` DATETIME NULL ,
  unique(`name`,`email`),
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;
