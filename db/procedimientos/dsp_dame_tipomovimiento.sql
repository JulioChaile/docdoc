DROP PROCEDURE IF EXISTS `dsp_dame_tipomovimiento`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_tipomovimiento`(pIdTipoMov int)
BEGIN
	/*
    Permite instanciar un tipo de movimiento desde la base de datos.
    */
    
    SELECT	*
    FROM	TiposMovimiento
    WHERE	IdTipoMov = pIdTipoMov;
END $$
DELIMITER ;
