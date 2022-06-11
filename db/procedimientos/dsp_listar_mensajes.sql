DROP PROCEDURE IF EXISTS `dsp_listar_mensajes`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_mensajes`(pIdChat bigint, pIdUltimoMensaje bigint, pLimit int, pOffset int)
PROC: BEGIN
    SET pLimit = COALESCE(pLimit, 100);
    SET pOffset = COALESCE(pOffset, 0);
    SELECT  *
    FROM    Mensajes
    WHERE   IdChat = pIdChat
            AND (pIdUltimoMensaje IS NULL OR IdMensaje > pIdUltimoMensaje)
    ORDER BY IdMensaje DESC
    LIMIT   pOffset, pLimit;
END $$
DELIMITER ;
