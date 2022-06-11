
DROP PROCEDURE IF EXISTS `dsp_listar_objetivos_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_objetivos_caso`(pIdCaso bigint)
BEGIN
	/*
    Permite listar los objetivos de un caso. Ordena por FechaAlta.
    */
    SELECT * FROM Objetivos WHERE IdCaso = pIdCaso;
END $$
DELIMITER ;
