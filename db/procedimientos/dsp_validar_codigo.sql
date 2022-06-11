DROP PROCEDURE IF EXISTS `dsp_validar_codigo`;
DELIMITER $$
CREATE PROCEDURE `dsp_validar_codigo`(pUsuario varchar(120), pEsValido char(1), 
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
BEGIN
	/*
    Permite validar el código de verificación enviado al usuario en el frontend. 
    Actualiza el Token del usuario y devuelve el password hash.
    */
    
    IF EXISTS (SELECT IdUsuario FROM Usuarios WHERE Usuario = pUsuario AND DebeCambiarPass = 'S' AND pEsValido = 'S') THEN
            SELECT 'OK' Mensaje, Password FROM Usuarios WHERE Usuario = pUsuario;
	ELSE
		SELECT 'Error al verificar el código' Mensaje, NULL Password;
	END IF;
END $$
DELIMITER ;
