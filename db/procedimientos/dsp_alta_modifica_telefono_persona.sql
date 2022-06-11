DROP PROCEDURE IF EXISTS `dsp_alta_modifica_telefono_persona`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_modifica_telefono_persona`(pJWT varchar(500), pIdPersona int, pTelefono varchar(20), pDetalle varchar(200),
			pEsPrincipal char(1), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite dar de alta un teléfono a una persona siempre y cuando el mismo no exista ya.
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdEstudio int;
    DECLARE pUsuario varchar(100);    
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			-- show errors;
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
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdPersona FROM Personas WHERE IdPersona = pIdPersona AND IdEstudio = pIdEstudio) THEN
		SELECT 'La persona indicada no existe en el estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdPersona FROM TelefonosPersona WHERE IdPersona = pIdPersona AND Telefono = pTelefono AND Detalle = pDetalle AND EsPrincipal = pEsPrincipal) THEN
		SELECT 'El teléfono indicado ya fue cargado.' Mensaje;
		LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);

        UPDATE  TelefonosPersona
        SET     EsPrincipal=IF(Telefono = pTelefono, pEsPrincipal, IF(pEsPrincipal = 'S', 'N', EsPrincipal)),
                Detalle=IF(Telefono = pTelefono, pDetalle, Detalle)
        WHERE   IdPersona = pIdPersona;

        IF NOT EXISTS (SELECT IdPersona FROM TelefonosPersona WHERE IdPersona = pIdPersona AND Telefono = pTelefono) THEN
            IF EXISTS (SELECT IdPersona FROM TelefonosPersona WHERE IdPersona = pIdPersona AND EsPrincipal = 'S') THEN
                UPDATE  TelefonosPersona
                SET     EsPrincipal = IF(pEsPrincipal = 'S', 'N', EsPrincipal)
                WHERE   IdPersona = pIdPersona;
            END IF;

            INSERT INTO TelefonosPersona VALUES(pIdPersona, pTelefono, NOW(), pDetalle, pEsPrincipal);

            INSERT INTO aud_TelefonosPersona
            SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA#TELEFONO', 'I', TelefonosPersona.* 
            FROM TelefonosPersona WHERE IdPersona = pIdPersona AND Telefono = pTelefono;
        END IF;

        INSERT INTO aud_TelefonosPersona
        SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'MODIFICA#TELEFONO', 'D', TelefonosPersona.* 
        FROM TelefonosPersona WHERE IdPersona = pIdPersona;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
