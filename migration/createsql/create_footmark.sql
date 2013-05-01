CREATE  TABLE IF NOT EXISTS `footmark` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `u_id` INT NOT NULL,
  `a_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL ,
  `updated_at` DATETIME NULL ,
  foreign key(a_id) references answer(id) on delete cascade,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;
