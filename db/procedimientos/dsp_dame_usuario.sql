DROP PROCEDURE IF EXISTS `dsp_dame_usuario`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_usuario`(pIdUsuario int)
PROC: BEGIN
	/*
    Permite instanciar un usuario desde la base de datos.
    */
    
    SELECT	IdUsuario, Nombres, Apellidos, Usuario, Token, Email, IntentosPass,
			FechaUltIntento, FechaAlta, DebeCambiarPass, Estado, Observaciones
    FROM 	Usuarios
    WHERE	IdUsuario = pIdUsuario;
END $$
DELIMITER ;
