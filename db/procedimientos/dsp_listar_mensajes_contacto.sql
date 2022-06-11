DROP PROCEDURE IF EXISTS `dsp_listar_mensajes_contacto`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_mensajes_contacto`(pIdChat bigint, pIdUltimoMensaje bigint, pLimit int, pOffset int)
PROC: BEGIN
    SET pLimit = COALESCE(pLimit, 20);
    SET pOffset = COALESCE(pOffset, 0);
    SELECT  *
    FROM    MensajesChatsContactos
    WHERE   IdChatContacto = pIdChat
            AND (pIdUltimoMensaje IS NULL OR IdMensaje > pIdUltimoMensaje)
    ORDER BY IdMensaje DESC
    LIMIT   pOffset, pLimit;
END $$
DELIMITER ;
