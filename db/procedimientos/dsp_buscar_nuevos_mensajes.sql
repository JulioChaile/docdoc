set collation_connection = utf8mb4_unicode_ci;
DROP PROCEDURE IF EXISTS `dsp_buscar_nuevos_mensajes`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_nuevos_mensajes`(pIdUsuario int)
PROC: BEGIN
    /*
    Permite obtener todos los mensajes no leídos del usuario.
    Para saber si el usuario pertenece a un chat verifica
    si el usuario mandó algún mensaje en el chat.
    */
    DECLARE pIdEstudio int;

    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario);

    SELECT DISTINCT     c.*, m.*, ca.Caratula, ca.NroExpediente, ca.Estado, ca.IdJuzgado, o.Origen
    FROM 		        Chats c
    INNER JOIN 	        Mensajes m USING (IdChat)
    INNER JOIN          Casos ca USING (IdCaso)
    INNER JOIN          UsuariosCaso uc USING (IdCaso)
    INNER JOIN          UsuariosEstudio ue ON ue.IdUsuario = uc.IdUsuario
    LEFT JOIN           Origenes o USING(IdOrigen)
    WHERE 		        m.IdMensaje > c.IdUltimoMensajeLeido AND
                        ue.IdEstudio = pIdEstudio AND
                        m.IdUsuario IS NULL
    ORDER BY            m.IdMensaje DESC;
END $$
DELIMITER ;
