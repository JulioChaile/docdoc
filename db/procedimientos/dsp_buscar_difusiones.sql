DROP PROCEDURE IF EXISTS `dsp_buscar_difusiones`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_difusiones`(pCadena varchar(50))
BEGIN
	/*
    Permite buscar campañas de difusión filtrándolas por nombre. 
    */
    SET pCadena = COALESCE(pCadena,'');
    SELECT		IdDifusion, Difusion, DATE_FORMAT(FechaInicio,'%d/%m/%Y') FechaInicio,
				DATE_FORMAT(FechaFin,'%d/%m/%Y') FechaFin
	FROM		Difusiones
    WHERE		Difusion LIKE CONCAT('%', pCadena,'%')
    ORDER BY	Difusion;
END $$
DELIMITER ;
