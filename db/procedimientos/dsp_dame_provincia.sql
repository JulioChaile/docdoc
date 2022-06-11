DROP PROCEDURE IF EXISTS `dsp_dame_provincia`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_provincia`(pIdProvincia int)
BEGIN
	/*
	Permite instanciar una provincia desde la base de datos.
    */
    SELECT	*
    FROM	Provincias
    WHERE	IdProvincia = pIdProvincia;
END $$
DELIMITER ;
