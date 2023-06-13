DROP PROCEDURE IF EXISTS `dsp_alta_caso_wp`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_caso_wp`(pApellidos varchar(100), pNombres varchar(100), pTelefono varchar(15), pIdChatApi text, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite dar de alta una consulta desde la web. 
    Devuelve OK + id de la consulta creada o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioCaso, pIdEstudio, pIdPersona, pIdObjetivo int;
    DECLARE pIdCaso, pIdMovimientoCaso, pIdChat bigint;
    DECLARE pDomicilio varchar(500);
    START TRANSACTION;
        -- Guardo como un caso nuevo con estado pendiente
        SET pIdCaso = (SELECT COALESCE(MAX(IdCaso),0) + 1 FROM Casos);
        
        INSERT INTO Casos VALUES(pIdCaso, 12, null, 10, 15, 16, 21, 8, CONCAT(pApellidos, ', ', pNombres), null, NOW(),
								null, NOW(), '', NULL, 'A', null);

        SET pIdEstudio = 5;
		
        SET pIdUsuarioCaso = (SELECT COALESCE(MAX(IdUsuarioCaso),0) FROM UsuariosCaso);
        
        SET @a = pIdUsuarioCaso;
        
        INSERT IGNORE INTO UsuariosCaso
        SELECT 	@a := @a + 1, pIdCaso, pIdEstudio, IdUsuario, 'A', 'S'
        FROM 	UsuariosEstudio WHERE IdEstudio = pIdEstudio; 
        
        INSERT INTO aud_UsuariosCaso
		SELECT 0, NOW(), 'Consulta', pIP, pUserAgent, pApp, 'ALTA#CASO', 'I', UsuariosCaso.* 
		FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio;

        -- Guardo la persona en el caso
        SET pIdPersona = (SELECT COALESCE(MAX(IdPersona),0) + 1 FROM Personas);
            
        INSERT INTO Personas VALUES(pIdPersona, pIdEstudio, 'F', pNombres, pApellidos, 
                                   '', '', '', '', NOW(), 'A', NULL);

        INSERT INTO aud_Personas
        SELECT 0, NOW(), 'Consulta', pIP, pUserAgent, pApp, 'ALTA#PERSONA#CASO', 'I', Personas.* 
        FROM Personas WHERE IdPersona = pIdPersona AND IdEstudio = pIdEstudio;

        INSERT INTO PersonasCaso VALUES(pIdCaso, pIdPersona, NULL, 'S', 'Actor', null, null);

		INSERT INTO aud_PersonasCaso
		SELECT 0, NOW(), 'Consulta', pIP, pUserAgent, pApp, 'ALTA#PERSONA#CASO', 'I', PersonasCaso.* 
		FROM PersonasCaso WHERE IdCaso = pIdCaso AND IdPersona = pIdPersona;

        -- Guardo el telefono
        INSERT INTO TelefonosPersona VALUES(pIdPersona, pTelefono, NOW(), 'Consulta de DocDoc', 'S');

        INSERT INTO Chats (IdExternoChat, IdCaso, IdPersona, Telefono, IdUltimoMensajeLeido)
        VALUES (pIdChatApi, pIdCaso, pIdPersona, pTelefono, NULL);

        SET pIdChat = LAST_INSERT_ID();

        INSERT INTO Mensajes
        SELECT DISTINCT 0, IdMensajeApi, pIdChat, Contenido, IdUsuario, FechaEnviado, FechaRecibido, FechaVisto
        FROM MensajesExterno
        WHERE IdChatApi = pIdChatApi
        GROUP BY IdMensajeApi;

        -- Guardo la consulta como movimiento del caso
        SET pIdUsuarioCaso = (SELECT COALESCE(MAX(IdUsuarioCaso),0) FROM UsuariosCaso WHERE IdCaso = pIdCaso AND IdEstudio = pIdEstudio);
        
        INSERT INTO MovimientosCaso VALUES (0, pIdCaso, 236, pIdUsuarioCaso, null, 'Consulta por Whatsapp',
											NOW(), NOW(), null, null, null, null, 'primary');
        
        SET pIdMovimientoCaso = LAST_INSERT_ID();
        
        INSERT INTO aud_MovimientosCaso
		SELECT 0, NOW(), 'Consulta', pIP, pUserAgent, pApp, 'ALTA', 'I', MovimientosCaso.* 
		FROM MovimientosCaso WHERE IdMovimientoCaso = pIdMovimientoCaso;

        -- Creo y asocio un objetivo 'Consulta' al movimiento
        SET pIdObjetivo = (SELECT COALESCE(MAX(IdObjetivo),0) + 1 FROM Objetivos);
        
        INSERT INTO Objetivos VALUES(pIdObjetivo, pIdCaso, 'Consulta de DocDoc', NOW());

        INSERT INTO MovimientosObjetivo VALUES (pIdObjetivo, pIdMovimientoCaso);
        
        SELECT CONCAT('OK', pIdCaso);
	COMMIT;
END $$
DELIMITER ;
