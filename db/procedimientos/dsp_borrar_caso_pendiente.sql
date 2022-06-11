DROP PROCEDURE IF EXISTS `dsp_borrar_caso_pendiente`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_caso_pendiente`(pJWT varchar(500), pIdCasoPendiente int, pIdEstudio int)
PROC: BEGIN
	/*
    Permite borrar un mediador controlando que no tenga roles asociados. 
    Devuelve OK o un mensaje de error en Mensaje. 
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pIdCaso, pIdCasoEstudio bigint;
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
    IF pIdCasoPendiente IS NULL OR pIdCasoPendiente = '' THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdEstudio IS NULL OR pIdEstudio = '' THEN
		SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM CasosPendientes WHERE IdCasoPendiente = pIdCasoPendiente) THEN
		SELECT 'El caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM CasosPendientes WHERE IdCasoPendiente = pIdCasoPendiente AND IdEstudio = pIdEstudio) THEN
		SELECT 'El caso no pertenece a este estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        SET pIdCaso = (SELECT IdCaso FROM CasosPendientes WHERE IdCasoPendiente = pIdCasoPendiente);

        /*
        DELETE FROM CasosPendientes
        WHERE       IdCasoPendiente = pIdCasoPendiente;
        */

        UPDATE  CasosPendientes
        SET     IdEstadoCasoPendiente = 36
        WHERE   IdCasoPendiente = pIdCasoPendiente;

        UPDATE  Casos
        SET     Estado = 'A'
        WHERE   IdCaso = pIdCaso;

        IF EXISTS (SELECT 1 FROM IdsCasosEstudio WHERE IdEstudio = pIdEstudio) THEN
            SET pIdCasoEstudio = (SELECT MAX(IdCasoEstudio) + 1 FROM IdsCasosEstudio WHERE IdEstudio = pIdEstudio);
        ELSE
            SET pIdCasoEstudio = 1;
        END IF;

        INSERT INTO IdsCasosEstudio SELECT pIdCasoEstudio, pIdCaso, pIdEstudio;
       
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
