set collation_connection=utf8mb4_unicode_ci;
DROP PROCEDURE IF EXISTS `dsp_modifica_telefono_persona`;
DELIMITER $$
CREATE PROCEDURE `dsp_modifica_telefono_persona`(pJWT varchar(500), pIdPersona int, pTelefono varchar(20), pTelefonoOld varchar(20), pDetalle varchar(200),
			pEsPrincipal char(1), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
    /*
    Permite modificar el telefono de una persona
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
    DECLARE pUsuario varchar(100);    
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			show errors;
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
	-- Validación de sesión
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'Ocurrió un problema con su sesión.' Mensaje;
        LEAVE PROC;
	END IF;
    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuarioGestion AND Estado = 'A');
    IF pIdEstudio IS NULL THEN
		SELECT 'El usuario no está activo en ningún estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros vacíos
    IF pIdPersona IS NULL THEN
		SELECT 'Debe indicar la persona.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pTelefono IS NULL OR pTelefono = '' THEN
		SELECT 'Debe indicar un teléfono.' Mensaje;
        LEAVE PROC;
	END IF;
    SET pEsPrincipal = IF(pEsPrincipal != 'S', 'N', 'S');
    IF NOT EXISTS (SELECT IdPersona FROM Personas WHERE IdPersona = pIdPersona AND IdEstudio = pIdEstudio) THEN
		SELECT 'La persona indicada no existe en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;

    START TRANSACTION;
        SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);

        IF pTelefono = pTelefonoOld THEN
            UPDATE TelefonosPersona
            SET Detalle = pDetalle,
                EsPrincipal = pEsPrincipal
            WHERE IdPersona = pIdPersona AND Telefono = pTelefono COLLATE utf8mb4_unicode_ci;
        ELSE
            UPDATE TelefonosPersona
            SET Telefono = pTelefono,
                Detalle = pDetalle,
                EsPrincipal = pEsPrincipal
            WHERE IdPersona = pIdPersona AND Telefono = pTelefonoOld COLLATE utf8mb4_unicode_ci;
        END IF;

        INSERT INTO aud_TelefonosPersona
        SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICA#TELEFONO', 'D', TelefonosPersona.* 
        FROM TelefonosPersona WHERE IdPersona = pIdPersona AND Telefono = pTelefonoOld COLLATE utf8mb4_unicode_ci;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
