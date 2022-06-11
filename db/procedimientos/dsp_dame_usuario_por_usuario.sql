DROP PROCEDURE IF EXISTS `dsp_dame_usuario_por_usuario`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_usuario_por_usuario`(pUsuario varchar(120))
BEGIN
	/*
    Permite instanciar un usuario por Usuario desde la base de datos.
    */
    SELECT		*
    FROM		Usuarios
    WHERE		Usuario = pUsuario;
END $$
DELIMITER ;
