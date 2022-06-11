DROP PROCEDURE IF EXISTS `dsp_registrar_comparticion_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_registrar_comparticion_caso`(pIdComparticion bigint, pIdCaso int, 
			pEmail varchar(120), pTokenMensaje text, pFechaEnviado datetime, pFechaRecibido datetime,
            pIdUsuarioOrigen int, pIdUsuarioDestino int, pIdEstudioDestino int, pIdEstudioOrigen int)
PROC: BEGIN
	/*
	Permite registrar una compartición de caso.
    */
    DECLARE pIdCasoEstudio bigint;
	-- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
        ROLLBACK;
    END;
    START TRANSACTION;
        IF pIdComparticion IS NULL THEN
            IF pIdEstudioDestino IS NOT NULL THEN
                IF EXISTS (SELECT 1 FROM IdsCasosEstudio WHERE IdEstudio = pIdEstudioDestino) THEN
                    SET pIdCasoEstudio = (SELECT MAX(IdCasoEstudio) + 1 FROM IdsCasosEstudio WHERE IdEstudio = pIdEstudioDestino);
                ELSE
                    SET pIdCasoEstudio = 1;
                END IF;

                INSERT INTO IdsCasosEstudio SELECT pIdCasoEstudio, pIdCaso, pIdEstudioDestino;

                IF EXISTS (SELECT 1 FROM CasosPendientes WHERE IdCaso = pIdCaso) THEN
                    INSERT INTO CasosPendientes
                    SELECT      0, Apellidos, Nombres, Domicilio, Telefono, Prioridad, IdEstadoCasoPendiente, IdOrigen,
                                Documento, Visitado, Latitud, Longitud, pIdEstudioDestino, FechaAlta, Lesion, IdCaso,
                                IdPersona, FechaEstado, FechaVisitado, IdUsuarioVisita
                    FROM        CasosPendientes
                    WHERE       IdCaso = pIdCaso;
                END IF;
            END IF;

            INSERT INTO Comparticiones SELECT 0, pIdCaso, pEmail, pTokenMensaje, pFechaEnviado, pFechaRecibido,
            pIdUsuarioOrigen, pIdUsuarioDestino, pIdEstudioDestino, pIdEstudioOrigen;
            SET pIdComparticion = LAST_INSERT_ID();
        ELSE
            UPDATE  Comparticiones
            SET     FechaRecibido = pFechaRecibido,
                    IdUsuarioDestino = pIdUsuarioDestino,
                    IdEstudioDestino = pIdEstudioDestino
            WHERE   IdComparticion = pIdComparticion;

            IF EXISTS (SELECT 1 FROM IdsCasosEstudio WHERE IdEstudio = pIdEstudioDestino) THEN
                SET pIdCasoEstudio = (SELECT MAX(IdCasoEstudio) + 1 FROM IdsCasosEstudio WHERE IdEstudio = pIdEstudioDestino);
            ELSE
                SET pIdCasoEstudio = 1;
            END IF;

            INSERT INTO IdsCasosEstudio SELECT pIdCasoEstudio, pIdCaso, pIdEstudioDestino;

            IF EXISTS (SELECT 1 FROM CasosPendientes WHERE IdCaso = pIdCaso) THEN
                INSERT INTO CasosPendientes
                SELECT      0, Apellidos, Nombres, Domicilio, Telefono, Prioridad, IdEstadoCasoPendiente, IdOrigen,
                            Documento, Visitado, Latitud, Longitud, pIdEstudioDestino, FechaAlta, Lesion, IdCaso,
                            IdPersona, FechaEstado, FechaVisitado, IdUsuarioVisita
                FROM        CasosPendientes
                WHERE       IdCaso = pIdCaso;
            END IF;
        END IF;
        
        SELECT CONCAT('OK', pIdComparticion) Mensaje;
    COMMIT;
END $$
DELIMITER ;
