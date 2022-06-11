DROP PROCEDURE IF EXISTS `dsp_eval`;
DELIMITER $$
CREATE PROCEDURE `dsp_eval`(pCadena mediumtext)
BEGIN
	/*
    Permite ejecutar una sentencia preparada leída de la base de datos, como implementación de polimorfismo.
    */
    -- SELECT pCadena;
	SET @Cadena = pCadena;
    PREPARE STMT FROM @Cadena;
    EXECUTE STMT;
END $$
DELIMITER ;
