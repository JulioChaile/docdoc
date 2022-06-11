DROP PROCEDURE IF EXISTS `dsp_listar_origenes`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_origenes`()
BEGIN
	/*
    Permite listar todos los origenes.
    */
    
    SELECT 	*
    FROM	Origenes;
END $$
DELIMITER ;
