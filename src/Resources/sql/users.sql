-- -----------------------------------------------------
-- Table `users`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users`.`user` (
  `id_user` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `username` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `roles` TEXT NOT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `confirmation_token` VARCHAR(255) NULL,
  `last_login` TIMESTAMP NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
  ENGINE = InnoDB
