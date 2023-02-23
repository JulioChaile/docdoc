DROP PROCEDURE IF EXISTS `dsp_listar_origenes`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_origenes`( pIdEstudio int )
BEGIN
	/*
    Permite listar todos los origenes.
    */
    
    SELECT 	*
    FROM	Origenes
    WHERE   IdEstudio = pIdEstudio;
END $$
DELIMITER ;
