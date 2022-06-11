DROP PROCEDURE IF EXISTS `dsp_buscar_nuevos_mensajes_mediador`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_nuevos_mensajes_mediador`(pIdUsuario int)
PROC: BEGIN
    /*
    Permite obtener todos los mensajes no leídos del usuario.
    Para saber si el usuario pertenece a un chat verifica
    si el usuario mandó algún mensaje en el chat.
    */
    SELECT 		c.*, m.*, me.Nombre
    FROM 		ChatsMediadores c
    INNER JOIN 	MensajesChatsMediadores m USING (IdChatMediador)
    INNER JOIN  Mediadores me USING (IdMediador)
    WHERE 		m.IdMensaje > c.IdUltimoMensajeLeido
				AND EXISTS (
					SELECT 	1
                    FROM 	MensajesChatsMediadores m2
                    WHERE 	m2.IdUsuario = pIdUsuario
							AND m2.IdChatMediador = c.IdChatMediador
				)
                AND m.IdUsuario IS NULL
    ORDER BY m.IdMensaje DESC;
END $$
DELIMITER ;
