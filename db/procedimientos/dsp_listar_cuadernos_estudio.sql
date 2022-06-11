DROP PROCEDURE IF EXISTS `dsp_listar_cuadernos_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_cuadernos_estudio`(pIdEstudio int)
BEGIN
	/*
    Permite listar los objetivos por defecto de un estudio. Ordena por EstadoCaso.
    */
    
    SELECT	*
    FROM	CuadernosEstudio
    WHERE	IdEstudio = pIdEstudio;
END $$
DELIMITER ;