DROP PROCEDURE IF EXISTS `dsp_dame_usuario_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_usuario_caso`(pIdUsuario int, pIdCaso bigint)
BEGIN
	/*
	Permite instanciar un usuario caso desde la base de datos.
    */
	SELECT * FROM UsuariosCaso WHERE IdUsuario = pIdUsuario AND IdCaso = pIdCaso;
END $$
DELIMITER ;
