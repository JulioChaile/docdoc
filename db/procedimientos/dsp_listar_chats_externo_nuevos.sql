DROP PROCEDURE IF EXISTS `dsp_listar_chats_externo_nuevos`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_chats_externo_nuevos`()
PROC: BEGIN
    DROP TEMPORARY TABLE IF EXISTS AUXM;
    CREATE TEMPORARY TABLE AUXM
    SELECT *
    FROM MensajesExterno
    WHERE FechaVisto IS NULL AND IdUsuario IS NULL
    ORDER BY IdMensajeExterno DESC;

    SELECT IdChatApi, FechaEnviado Fecha, COUNT(IdChatApi) MensajesSinLeer
    FROM AUXM
    GROUP BY IdChatApi;
    DROP TEMPORARY TABLE IF EXISTS AUXM;
END $$
DELIMITER ;
