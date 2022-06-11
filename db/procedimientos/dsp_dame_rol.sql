DROP PROCEDURE IF EXISTS `dsp_dame_rol`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_rol`(pIdRol int)
BEGIN
	/*
    Procedimiento que sirve para instanciar un rol desde la base de datos.
    */
	SELECT	*
    FROM	Roles
    WHERE	IdRol = pIdRol;
END $$
DELIMITER ;
