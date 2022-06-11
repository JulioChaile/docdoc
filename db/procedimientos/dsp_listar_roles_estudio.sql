DROP PROCEDURE IF EXISTS `dsp_listar_roles_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_roles_estudio`(pIdEstudio int)
BEGIN
	/*
    Permite listar todos los roles de un estudio.
    */
    SELECT * FROM RolesEstudio WHERE IdEstudio = pIdEstudio;
END $$
DELIMITER ;
