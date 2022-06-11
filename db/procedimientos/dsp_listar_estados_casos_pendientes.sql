DROP PROCEDURE IF EXISTS `dsp_listar_estados_casos_pendientes`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_estados_casos_pendientes`()
BEGIN
	/*
    Permite listar todos los origenes.
    */
    
    SELECT 	    *
    FROM	    EstadosCasoPendiente
    WHERE       IdEstadoCasoPendiente != 36
    ORDER BY    EstadoCasoPendiente ASC;
END $$
DELIMITER ;
