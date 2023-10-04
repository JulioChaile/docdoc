DROP PROCEDURE IF EXISTS `dsp_dame_tablero_movimiento`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_tablero_movimiento`(pIdTipoMovimientoTablero int)
BEGIN
	/*
    Permite instanciar un origen de casos desde la base de datos.
    */
    
    SELECT	*
    FROM	TiposMovimientoTableros
    WHERE	IdTipoMovimientoTablero = pIdTipoMovimientoTablero;
END $$
DELIMITER ;
