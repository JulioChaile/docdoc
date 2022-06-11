DROP PROCEDURE IF EXISTS `dsp_listar_chats_externo_nuevos`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_chats_externo_nuevos`()
PROC: BEGIN
    SELECT IdChatApi, COUNT(IdChatApi) MensajesSinLeer FROM MensajesExterno WHERE FechaVisto IS NULL AND IdUsuario IS NULL GROUP BY IdChatApi;
END $$
DELIMITER ;
