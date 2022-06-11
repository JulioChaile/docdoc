DROP PROCEDURE IF EXISTS `dsp_dame_ciudad`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_ciudad`(pIdCiudad int)
BEGIN
	/*
    Permite instanciar una ciudad desde la base de datos.
    */
    SELECT	*
    FROM	Ciudades 
    WHERE	IdCiudad = pIdCiudad;
END $$
DELIMITER ;
