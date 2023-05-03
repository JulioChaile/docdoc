DROP PROCEDURE IF EXISTS `dsp_alta_caso_pendiente`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_caso_pendiente`(pJWT varchar(500), pIdEstudio int, pNombres varchar(100), pApellidos varchar(45), pTelefono varchar(200), pDomicilio varchar(100), pPrioridad char(1), pIdEstadoCasoPendiente int, pIdOrigen int, pDocumento varchar(10), pLatitud varchar(45), pLongitud varchar(45), pLesion varchar(45))
PROC: BEGIN
	/*
    Permite crear contactos 
    Devuelve OK + Id del contacto creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdPersona, pIdUsuarioCaso int;
    DECLARE pIdCaso bigint;
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
    IF pApellidos IS NULL OR pApellidos = '' THEN
		SELECT 'Debe indicar el apellido.' Mensaje;
        LEAVE PROC;
	END IF;
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

    START TRANSACTION;
        SET pIdCaso = (SELECT COALESCE(MAX(IdCaso),0) + 1 FROM Casos);
        SET pIdPersona = (SELECT COALESCE(MAX(IdPersona),0) + 1 FROM Personas);
                
        INSERT INTO CasosPendientes  SELECT 0, pApellidos, pNombres, pDomicilio, pTelefono, pPrioridad, pIdEstadoCasoPendiente, pIdOrigen, pDocumento,
                                            NULL, pLatitud, pLongitud, pIdEstudio, NOW(), pLesion, pIdCaso, pIdPersona, NOW(), null, null;

        -- Guardo como un caso nuevo
        INSERT INTO Casos VALUES(pIdCaso, 12, null, 10, 15, null, 21, pIdOrigen, CONCAT(pApellidos, ', ', pNombres), null, NOW(),
								null, NOW(), '', NULL, 'P');
		
        SET pIdUsuarioCaso = (SELECT COALESCE(MAX(IdUsuarioCaso),0) FROM UsuariosCaso);
        
        SET @a = pIdUsuarioCaso;
        
        INSERT IGNORE INTO UsuariosCaso
        SELECT 	@a := @a + 1, pIdCaso, pIdEstudio, IdUsuario, 'A', 'S'
        FROM 	UsuariosEstudio WHERE IdEstudio = pIdEstudio; 
        
        INSERT INTO aud_UsuariosCaso
		SELECT 0, NOW(), 'Caso Pendiente', 0, 0, 'C', 'ALTA#CASO', 'I', UsuariosCaso.* 
		FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio;

        -- Guardo la persona en el caso
        INSERT INTO Personas VALUES(pIdPersona, pIdEstudio, 'F', pNombres, pApellidos, 
                                   '', pDocumento, '', pDomicilio, NOW(), 'A');

        INSERT INTO aud_Personas
        SELECT 0, NOW(), 'Caso Pendiente', 0, 0, 'C', 'ALTA#PERSONA#CASO', 'I', Personas.* 
        FROM Personas WHERE IdPersona = pIdPersona AND IdEstudio = pIdEstudio;

        INSERT INTO PersonasCaso VALUES(pIdCaso, pIdPersona, NULL, 'S', 'Actor', null, null);

		INSERT INTO aud_PersonasCaso
		SELECT 0, NOW(), 'Caso Pendiente', 0, 0, 'C', 'ALTA#PERSONA#CASO', 'I', PersonasCaso.* 
		FROM PersonasCaso WHERE IdCaso = pIdCaso AND IdPersona = pIdPersona;

        -- Guardo el telegono
        INSERT INTO TelefonosPersona VALUES(pIdPersona, pTelefono, NOW(), 'Caso Pendiente', 'S');

        -- Ingreso objetivos por defecto
        INSERT IGNORE INTO Objetivos
        SELECT @o := @o + 1, pIdCaso, oe.ObjetivoEstudio, NOW()
        FROM ObjetivosEstudio oe, (SELECT @o := MAX(IdObjetivo) FROM Objetivos) s
        WHERE oe.IdEstudio = pIdEstudio;
        
        SELECT CONCAT('OK') Mensaje;
	COMMIT;
END $$
DELIMITER ;
