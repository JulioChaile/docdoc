
DROP PROCEDURE IF EXISTS `dsp_login`;
DELIMITER $$
CREATE PROCEDURE `dsp_login`(pUsuario varchar(120), 
			pEsPassValido char(1), pJWT varchar(500), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite realizar el login de un usuario indicando la aplicación a la que desea acceder en 
    pApp= A: Administración, C: Cliente (PWA) - E: Estudios. Recibe como parámetro la autenticidad del par Usuario - Password 
    en pEsPassValido [S | N]. Controla que el usuario no haya superado el límite de login's 
    erroneos posibles indicado en MAXINTPASS, caso contrario se cambia El estado de la cuenta a
    S: Suspendido. Un intento exitoso de inicio de sesión resetea el contador de intentos fallidos.
    Devuelve un mensaje con el resultado del login y un objeto usuario en caso de login exitoso.
    */
	DECLARE pMAXINTPASS int;
    DECLARE pIdUsuario int;
    -- Manejo de errores en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			show errors;
			SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
	-- Inicializo variables de empresa
    SET pMAXINTPASS = (SELECT Valor FROM Empresa WHERE Parametro = 'MAXINTPASS');
	-- Control de parámetros vacíos
    IF pApp NOT IN ('A', 'C', 'E', 'D') OR pApp IS NULL OR pApp = '' OR pEsPassValido NOT IN ('S','N') 
    OR pEsPassValido IS NULL OR pEsPassValido = '' THEN
		SELECT 'Parámetros incorrectos.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pUsuario IS NULL OR pUsuario = '' THEN
		SELECT 'Debe indicar un usuario.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdUsuario FROM Usuarios WHERE Usuario = pUsuario AND Estado = 'A') THEN
		SELECT 'El usuario indicado no existe en el sistema o se encuentra dado baja.' Mensaje;
        LEAVE PROC;
	END IF;
    
    IF pApp = 'A' AND EXISTS (SELECT IdUsuario FROM Usuarios WHERE Usuario = pUsuario AND (IdRol != 1 OR IdRol IS NULL)) THEN
		SELECT 'No tiene permisos para acceder a esta aplicación.' Mensaje;
        LEAVE PROC;
	END IF;
    
    SET pIdUsuario = (SELECT IdUsuario FROM Usuarios WHERE Usuario = pUsuario);
    IF pApp = 'A' AND NOT EXISTS (SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario 
    AND IdRol IS NOT NULL) THEN
		SELECT 'No tiene permiso para acceder a esta aplicación.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pApp IN ('C', 'E') AND EXISTS (SELECT IdUsuario FROM Usuarios WHERE Usuario = pUsuario AND IdRol IS NOT NULL) THEN
		SELECT 'Debe estar registrado en DocDoc-Gestión para poder ingresar.' Mensaje;
        LEAVE PROC;
	END IF;
	START TRANSACTION;
		
		CASE pEsPassValido 
        WHEN 'N' THEN 
			BEGIN
				IF (SELECT IntentosPass FROM Usuarios WHERE Usuario = pUsuario) < pMAXINTPASS THEN
					
                    -- Antes
					INSERT INTO aud_Usuarios
					SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'PASS#INVALIDO', 'A', Usuarios.* 
					FROM Usuarios WHERE IdUsuario = pIdUsuario;
                    
					UPDATE	Usuarios 
					SET		IntentosPass = IntentosPass + 1, FechaUltIntento = NOW()
					WHERE	Usuario = pUsuario;
                    
                    -- Después
					INSERT INTO aud_Usuarios
					SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'PASS#INVALIDO', 'D', Usuarios.* 
					FROM Usuarios WHERE IdUsuario = pIdUsuario;
					
					SELECT 'Usuario y/o contraseña incorrectos. Ante repetidos intentos fallidos de inicio de sesión, la cuenta se suspenderá.' Mensaje;
					COMMIT;
					LEAVE PROC;
				END IF;
				
				IF (SELECT IntentosPass FROM Usuarios WHERE Usuario = pUsuario) >= pMAXINTPASS THEN
				
					UPDATE	Usuarios
					SET		Estado = 'S', FechaUltIntento = NOW()
					WHERE	Usuario = pUsuario;
					
					SELECT 'Cuenta suspendida por superar cantidad máxima de intentos de inicio de sesión.' Mensaje;
					COMMIT;
					LEAVE PROC;
				END IF;
			END;
		WHEN 'S' THEN
			BEGIN
            
				-- Antes
				INSERT INTO aud_Usuarios
				SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'LOGIN', 'A', Usuarios.* 
				FROM Usuarios WHERE IdUsuario = pIdUsuario;
                
                UPDATE	Usuarios
                SET		Token = pJWT,
						FechaUltIntento = NOW(),
                        IntentosPass = 0
				WHERE	Usuario = pUsuario;
                
                -- Después
				INSERT INTO aud_Usuarios 
				SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'LOGIN', 'D', Usuarios.* 
				FROM Usuarios WHERE IdUsuario = pIdUsuario;
                
                COMMIT;
            END;
        END CASE;     
	CASE pApp
		WHEN 'A' THEN
			SELECT 		'OK' Mensaje, u.IdUsuario, u.IdRol, u.Nombres, u.Apellidos, u.Usuario,
						u.Token, u.Email, u.DebeCambiarPass, u.Estado, r.Rol
			FROM 		Usuarios u
            INNER JOIN 	Roles r ON r.IdRol = u.IdRol
			WHERE		Usuario = pUsuario;
		WHEN 'D' THEN
			SELECT 		'OK' Mensaje, u.IdUsuario, u.IdRol, u.Nombres, u.Apellidos, u.Usuario,
						u.Token, u.Email, u.DebeCambiarPass, u.Estado, r.Rol
			FROM 		Usuarios u
            INNER JOIN 	Roles r ON r.IdRol = u.IdRol
			WHERE		Usuario = pUsuario;
		WHEN 'C' OR 'E' THEN 
			SELECT 		'OK' Mensaje, u.IdUsuario, u.IdRol, u.Nombres, u.Apellidos, u.Usuario,
						u.Token, u.Email, u.DebeCambiarPass, ue.IdEstudio, e.Estudio, ue.Estado,
                        ue.IdUsuarioPadre, ue.IdRolEstudio, re.RolEstudio, u.Observaciones
			FROM 		Usuarios u
            INNER JOIN	UsuariosEstudio ue ON ue.IdUsuario = u.IdUsuario
            INNER JOIN	RolesEstudio re ON re.IdRolEstudio = ue.IdRolEstudio
            INNER JOIN	Estudios e ON e.IdEstudio = ue.IdEstudio
			WHERE		Usuario = pUsuario AND ue.Estado = 'A'; 
	END CASE;
END $$
DELIMITER ;
