CREATE  TABLE IF NOT EXISTS `good` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `u_id` INT NOT NULL,
  `a_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL ,
  `updated_at` DATETIME NULL ,
  unique(u_id, a_id),
  PRIMARY KEY (`id`))
ENGINE = InnoDB;
