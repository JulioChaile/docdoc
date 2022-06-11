DROP PROCEDURE IF EXISTS `dsp_listar_eventos_notificaciones`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_eventos_notificaciones`()
PROC: BEGIN
    SELECT      mc.IdMovimientoCaso, mc.Detalle, mc.FechaEsperada, c.Caratula, e.Comienzo FechaEvento, c.IdCaso, cts.IdChat, cts.IdExternoChat,
                DATEDIFF(mc.FechaEsperada, NOW()) Dias,
                JSON_ARRAYAGG(JSON_OBJECT(
                    'Nombres', p.Nombres,
                    'Apellidos', p.Apellidos,
                    'IdPersona', p.IdPersona,
                    'Documento', p.Documento,
                    'EsPrincipal', pc.EsPrincipal,
                    'Observaciones', pc.Observaciones,
                    'Parametros', pc.ValoresParametros,
                    'DocumentacionSolicitada', pc.DocumentacionSolicitada,
                    'TokenApp', utac.TokenApp
                )) PersonasCaso
    FROM        MovimientosClientes mcc
    LEFT JOIN   MovimientosCaso mc USING(IdMovimientoCaso)
    INNER JOIN  EventosMovimientos em USING(IdMovimientoCaso)
    INNER JOIN  Eventos e USING(IdEvento)
    INNER JOIN  Casos c ON mcc.IdCaso = c.IdCaso
    LEFT JOIN   Chats cts ON cts.IdCaso = c.IdCaso
    LEFT JOIN   PersonasCaso pc ON pc.IdCaso = c.IdCaso
    LEFT JOIN   Personas p ON p.IdPersona = pc.IdPersona
	LEFT JOIN	Usuarios u ON u.Usuario = p.Documento
	LEFT JOIN	UsuariosTokenAppCliente utac ON utac.IdUsuario = u.IdUsuario
    WHERE       DATEDIFF(mc.FechaEsperada, NOW()) IN (15, 10, 5, 2, 1) AND
				mc.FechaEsperada > NOW()
    GROUP BY	c.IdCaso, mc.IdMovimientoCaso, e.Comienzo, cts.IdChat;
END $$
DELIMITER ;
