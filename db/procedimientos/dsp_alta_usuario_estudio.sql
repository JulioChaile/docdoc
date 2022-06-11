DROP PROCEDURE IF EXISTS `dsp_alta_usuario_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_usuario_estudio`(pJWT varchar(500), pIdEstudio int, pIdUsuarioPadre int,
				pIdRolEstudio int, pNombres varchar(30), pApellidos varchar(30), pUsuario varchar(120), 
                pPassword varchar(255), pEmail varchar(120), pObservaciones varchar(255), pTelefono varchar(20), 
                pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear un usuario abogado controlando que el documento, email y nombre de usuario no se encuentre en uso ya.
    Asigna el usuario creado al estudio. 
    Devuelve OK + el id del usuario creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdUsuario int;
    DECLARE pMensaje varchar(100); 
    -- Manejo de errores en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
	-- Validación de sesión
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'Ocurrió un problema con su sesión.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdRolEstudio IS NULL THEN
		SELECT 'Debe indicar el rol del usuario del estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pNombres IS NULL OR pNombres = '' THEN
		SELECT 'Debe indicar un nombre.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pApellidos IS NULL OR pApellidos = '' THEN
		SELECT 'Debe indicar un apellido.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pUsuario IS NULL OR pUsuario = '' THEN
		SELECT 'Debe indicar un nombre de ususario.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pPassword IS NULL OR pPassword = '' THEN
		SELECT 'Password inválido.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pEmail IS NULL OR pEmail = '' THEN
		SELECT 'Debe indicar un email.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdUsuarioPadre IS NOT NULL AND NOT EXISTS(SELECT IdUsuario FROM UsuariosEstudio 
    WHERE IdEstudio = pIdEstudio AND IdUsuario = pIdUsuarioPadre AND Estado = 'A') THEN
		SELECT 'El usuario padre indicado no existe en el estudio o está dado de baja.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdRolEstudio FROM RolesEstudio WHERE IdEstudio = pIdEstudio AND IdRolEstudio = pIdRolEstudio) THEN
		SELECT 'El rol indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT Usuario FROM Usuarios WHERE Usuario = pUsuario) THEN
		SELECT 'El nombre de usuario indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT Email FROM Usuarios WHERE Email = pEmail) THEN
		SELECT 'El email indicado ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		
        CALL dsp_alta_usuario_comun(pJWT, NULL, pNombres, pApellidos, pUsuario, pPassword, pEmail, 
								pObservaciones, pTelefono, pIP, pUserAgent, pApp, pMensaje);
        
        IF SUBSTRING(pMensaje,1,2) = 'OK' THEN
			SET pIdUsuario = SUBSTRING(pMensaje,3);
			INSERT INTO UsuariosEstudio VALUES(pIdEstudio, pIdUsuario, pIdEstudio, pIdUsuarioPadre, pIdRolEstudio, 'A');
            
            SET @a = (SELECT MAX(IdUsuarioCaso) FROM UsuariosCaso);
            
            DROP TEMPORARY TABLE IF EXISTS tmp_casos;
            CREATE TEMPORARY TABLE tmp_casos
            SELECT IdCaso FROM UsuariosCaso WHERE IdEstudio = pIdEstudio GROUP BY IdCaso;
            
            INSERT INTO UsuariosCaso
            SELECT @a := @a + 1, IdCaso, pIdEstudio, pIdUsuario, 'E', 'N'
            FROM tmp_casos;
            
			SELECT pMensaje Mensaje;
			COMMIT;
		ELSE
			ROLLBACK;
			SELECT pMensaje Mensaje;
		END IF;
END $$
DELIMITER ;
