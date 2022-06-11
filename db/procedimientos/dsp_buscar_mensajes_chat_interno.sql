DROP PROCEDURE IF EXISTS `dsp_buscar_mensajes_chat_interno`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_mensajes_chat_interno`(pIdCaso int)
PROC: BEGIN
    SELECT      mci.*, u.Apellidos, u.Nombres, m.URL, m.Tipo
    FROM        MensajesChatInterno mci
    LEFT JOIN   Usuarios u USING(IdUsuario)
    LEFT JOIN   Multimedia m USING(IdMultimedia)
    WHERE       mci.IdCaso = pIdCaso
    ORDER BY    mci.IdMensajeChatInterno DESC
    LIMIT       50;
END $$
DELIMITER ;0
