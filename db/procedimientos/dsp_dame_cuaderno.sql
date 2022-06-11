DROP PROCEDURE IF EXISTS `dsp_dame_cuaderno`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_cuaderno`(pIdCuaderno int)
BEGIN
	/*
    Permite instanciar un estado de caso de un estudio desde la base de datos.
    */
    SELECT 	*
    FROM	CuadernosEstudio
    WHERE	IdCuaderno = pIdCuaderno;
END $$
DELIMITER ;
