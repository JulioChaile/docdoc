DROP PROCEDURE IF EXISTS `dsp_dame_validar_codigo`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_validar_codigo`(pUsuario varchar(120), pJWT varchar(500), 
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
BEGIN
	/*
    Permite validar el código de verificación enviado al usuario en el frontend. 
    Actualiza el Token del usuario y devuelve el password hash.
    */
    
    IF EXISTS (SELECT IdUsuario FROM Usuarios WHERE Usuario = pUsuario AND DebeCambiarPass = 'S') THEN
		START TRANSACTION;
			-- Antes
			INSERT INTO aud_Usuarios
			SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'VALIDAR', 'A', Usuarios.* 
			FROM Usuarios WHERE Usuario = pUsuario;
			UPDATE	Usuarios
			SET		Token = pJWT
			WHERE	Usuario = pUsuario;
		
			-- Después
			INSERT INTO aud_Usuarios
			SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'VALIDAR', 'D', Usuarios.* 
			FROM Usuarios WHERE Usuario = pUsuario;
			
            SELECT Password FROM Usuarios WHERE Usuario = pUsuario;
        COMMIT;        
	ELSE
		SELECT NULL Password;
	END IF;
END $$
DELIMITER ;
