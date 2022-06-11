DROP PROCEDURE IF EXISTS `dsp_listar_mensajes_externo`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_mensajes_externo`(pIdChatApi varchar(500))
PROC: BEGIN
    DECLARE pLimit int;
    DECLARE pOffset int;

    SET pLimit = 100;
    SET pOffset = 0;

    DROP TEMPORARY TABLE IF EXISTS TEMPCE;
    CREATE TEMPORARY TABLE TEMPCE
    SELECT  *
    FROM    MensajesExterno
    WHERE   IdChatApi = pIdChatApi AND (IdUsuario <> 1 OR IdUsuario IS NULL)
    ORDER BY IdMensajeExterno DESC
    LIMIT   pOffset, pLimit;

    UPDATE MensajesExterno
    SET FechaVisto = NOW()
    WHERE IdChatApi = pIdChatApi;

    SELECT * FROM TEMPCE;
END $$
DELIMITER ;
