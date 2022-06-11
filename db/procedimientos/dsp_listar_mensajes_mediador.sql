DROP PROCEDURE IF EXISTS `dsp_listar_mensajes_mediador`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_mensajes_mediador`(pIdChat bigint, pIdUltimoMensaje bigint, pLimit int, pOffset int)
PROC: BEGIN
    SET pLimit = COALESCE(pLimit, 20);
    SET pOffset = COALESCE(pOffset, 0);
    SELECT  *
    FROM    MensajesChatsMediadores
    WHERE   IdChatMediador = pIdChat
            AND (pIdUltimoMensaje IS NULL OR IdMensaje > pIdUltimoMensaje)
    ORDER BY IdMensaje DESC
    LIMIT   pOffset, pLimit;
END $$
DELIMITER ;
