DROP PROCEDURE IF EXISTS `dsp_buscar_parametros`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_parametros`(pCadena varchar(20), pIdEstudio int)
BEGIN
	/*
    Permite buscar parámetros de empresa filtrándolos por nombre del parámetro.
    */
    
    SELECT 	* 
    FROM 	Empresa 
    WHERE 	Parametro LIKE CONCAT('%', pCadena, '%') 
			AND EsEditable = 'S' 
			AND IdEstudio = pIdEstudio;
END $$
DELIMITER ;
