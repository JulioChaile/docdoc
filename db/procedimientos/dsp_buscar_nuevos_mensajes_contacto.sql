DROP PROCEDURE IF EXISTS `dsp_buscar_nuevos_mensajes_contacto`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_nuevos_mensajes_contacto`(pIdUsuario int)
PROC: BEGIN
    /*
    Permite obtener todos los mensajes no leídos del usuario.
    Para saber si el usuario pertenece a un chat verifica
    si el usuario mandó algún mensaje en el chat.
    */
    DECLARE pIdEstudio int;

    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario);

    SELECT DISTINCT     c.*, m.*, CONCAT(ct.Apellidos, ' ', ct.Nombres) Nombre
    FROM 		        ChatsContactos c
    INNER JOIN 	        MensajesChatsContactos m USING (IdChatContacto)
    INNER JOIN          ContactosEstudio ct USING (IdContacto)
    WHERE 		        m.IdMensaje > c.IdUltimoMensajeLeido
				        AND ct.IdEstudio = pIdEstudio
                        AND m.IdUsuario IS NULL
    ORDER BY            m.IdMensaje DESC;
END $$
DELIMITER ;
