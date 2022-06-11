DROP PROCEDURE IF EXISTS `dsp_dame_origen`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_origen`(pIdOrigen int)
BEGIN
	/*
    Permite instanciar un origen de casos desde la base de datos.
    */
    
    SELECT	*
    FROM	Origenes
    WHERE	IdOrigen = pIdOrigen;
END $$
DELIMITER ;
