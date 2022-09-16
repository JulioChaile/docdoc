CREATE TABLE `docdev`.`FotosCaso` (
  `IdFotoCaso` INT NOT NULL AUTO_INCREMENT,
  `IdCaso` BIGINT NOT NULL,
  `FotoCaso` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`IdFotoCaso`),
  INDEX `sdfdsfsd_idx` (`IdCaso` ASC) VISIBLE,
  CONSTRAINT `sdfdsfsd`
    FOREIGN KEY (`IdCaso`)
    REFERENCES `docdev`.`Casos` (`IdCaso`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
