DROP PROCEDURE IF EXISTS `dsp_listar_etiquetas_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_etiquetas_estudio`(pIdEstudio int)
BEGIN
	/*
    Permite listar los objetivos por defecto de un estudio. Ordena por EstadoCaso.
    */
    
    SELECT DISTINCT	Etiqueta
    FROM	        EtiquetasCaso
    WHERE	        IdEstudio = pIdEstudio;
END $$
DELIMITER ;