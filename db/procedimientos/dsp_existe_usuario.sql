DROP PROCEDURE IF EXISTS `dsp_existe_usuario`;
DELIMITER $$
CREATE PROCEDURE `dsp_existe_usuario`(pUsuario varchar(120))
BEGIN
	/*
    Devuelve S si el usuario existe o N si no existe.
    */
    SELECT IF(EXISTS(SELECT IdUsuario FROM Usuarios WHERE Usuario = pUsuario),'S','N') Mensaje;
END $$
DELIMITER ;
