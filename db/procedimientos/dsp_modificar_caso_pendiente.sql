DROP PROCEDURE IF EXISTS `dsp_modificar_caso_pendiente`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_caso_pendiente`(pJWT varchar(500), pIdEstudio int, pIdCasoPendiente int, pNombres varchar(100), pApellidos varchar(45), pTelefono varchar(200), pDomicilio varchar(100), pPrioridad char(1), pIdEstadoCasoPendiente int, pIdOrigen int, pDocumento varchar(10), pLatitud varchar(45), pLongitud varchar(45), pLesion varchar(45), pVisitado char(1))
PROC: BEGIN
	/*
    Permite crear contactos 
    Devuelve OK + Id del contacto creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdPersona int;
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
    IF pNombres IS NULL OR pNombres = '' THEN
		SELECT 'Debe indicar el nombre.' Mensaje;
        LEAVE PROC;
	END IF;
    /*
    IF pApellidos IS NULL OR pApellidos = '' THEN
		SELECT 'Debe indicar el apellido.' Mensaje;
        LEAVE PROC;
	END IF;
    */
    /*
    IF pDocumento IS NULL OR pDocumento = '' THEN
		SELECT 'Debe indicar el documento.' Mensaje;
        LEAVE PROC;
	END IF;
    */
    IF pIdEstadoCasoPendiente IS NULL OR pIdEstadoCasoPendiente = '' THEN
		SELECT 'Debe indicar el estado.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdOrigen IS NULL OR pIdOrigen = '' THEN
		SELECT 'Debe indicar el origen.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pPrioridad IS NULL OR pPrioridad = '' THEN
		SELECT 'Debe indicar la prioridad.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdEstudio IS NULL OR pIdEstudio = '' THEN
		SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'No existe el estudio indicado' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM EstadosCasoPendiente WHERE IdEstadoCasoPendiente = pIdEstadoCasoPendiente) THEN
		SELECT 'No existe el estado indicado' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Origenes WHERE IdOrigen = pIdOrigen) THEN
		SELECT 'No existe el origen indicado' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM CasosPendientes WHERE IdCasoPendiente = pIdCasoPendiente) THEN
		SELECT 'No existe el caso indicado' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM CasosPendientes WHERE IdCasoPendiente = pIdCasoPendiente AND IdEstudio = pIdEstudio) THEN
		SELECT 'El caso indicado no pertenece a este estudio' Mensaje;
        LEAVE PROC;
	END IF;

    START TRANSACTION;
        IF (pVisitado = 1 OR pVisitado = '1') AND EXISTS (SELECT 1 FROM CasosPendientes WHERE IdCasoPendiente = pIdCasoPendiente AND (Visitado = 0 OR Visitado = '0' OR Visitado = '' OR Visitado IS NULL)) THEN
            UPDATE  CasosPendientes
            SET     FechaVisitado = DATE(NOW()),
                    IdUsuarioVisita = pIdUsuarioGestion
            WHERE   IdCasoPendiente = pIdCasoPendiente;
        END IF;

        IF (pVisitado = 0 OR pVisitado = '0' OR pVisitado = '' OR pVisitado IS NULL) THEN
            UPDATE  CasosPendientes
            SET     IdUsuarioVisita = null
            WHERE   IdCasoPendiente = pIdCasoPendiente;
        END IF;

        IF NOT EXISTS (SELECT 1 FROM CasosPendientes WHERE IdCasoPendiente = pIdCasoPendiente AND IdEstadoCasoPendiente = pIdEstadoCasoPendiente) THEN
            UPDATE  CasosPendientes
            SET     FechaEstado = NOW()
            WHERE   IdCasoPendiente = pIdCasoPendiente;
        END IF;

        UPDATE  CasosPendientes
        SET     Nombres = pNombres,
                Apellidos = pApellidos,
                Domicilio = pDomicilio,
                Telefono = pTelefono,
                Prioridad = pPrioridad,
                IdEstadoCasoPendiente = pIdEstadoCasoPendiente,
                IdOrigen = pIdOrigen,
                Documento = pDocumento,
                Visitado = pVisitado,
                Latitud = pLatitud,
                Longitud = pLongitud,
                Lesion = pLesion
        WHERE   IdCasoPendiente = pIdCasoPendiente;

        SET pIdPersona = (SELECT IdPersona FROM CasosPendientes WHERE IdCasoPendiente = pIdCasoPendiente);

        UPDATE  TelefonosPersona
        SET     Telefono = pTelefono
        WHERE   IdPersona = pIdPersona AND EsPrincipal = 'S';

        UPDATE  Personas
        SET     Domicilio = pDomicilio
        WHERE   IdPersona = pIdPersona;
        
        SELECT CONCAT('OK') Mensaje;
	COMMIT;
END $$
DELIMITER ;
