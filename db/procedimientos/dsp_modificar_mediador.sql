DROP PROCEDURE IF EXISTS `dsp_modificar_mediador`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_mediador`(pJWT varchar(500), pIdMediador int, pNombre varchar(255), pRegistro tinyint(4), pMP varchar(500), pDomicilio varchar(200), pTelefono varchar(200), pEmail varchar(50))
PROC: BEGIN
	/*
    Permite modificar mediadors controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pUsuario varchar(120);
    -- Manejo de errores
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
    IF pNombre IS NULL OR pNombre = '' THEN
		SELECT 'Debe indicar el nombre de la mediador.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdMediador IS NULL THEN
		SELECT 'Debe indicar una mediador.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT 1 FROM Mediadores WHERE Nombre = pNombre AND IdMediador != pIdMediador) THEN
		SELECT 'El mediador indicado ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdMediador FROM Mediadores WHERE IdMediador = pIdMediador) THEN
		SELECT 'El mediador indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		UPDATE	Mediadores
        SET		Nombre = pNombre,
                Registro = pRegistro,
                MP = pMP,
                Domicilio = pDomicilio,
                Telefono = pTelefono,
                Email = pEmail
        WHERE	IdMediador = pIdMediador;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
