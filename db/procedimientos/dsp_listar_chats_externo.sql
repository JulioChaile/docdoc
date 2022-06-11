DROP PROCEDURE IF EXISTS `dsp_listar_chats_externo`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_chats_externo`()
PROC: BEGIN
    SELECT DISTINCT *  FROM (
        SELECT mx.IdChatApi
        FROM MensajesExterno mx
        LEFT JOIN Usuarios u ON SUBSTRING(mx.IdChatApi, LENGTH(mx.IdChatApi) - 8, 4) = SUBSTRING(u.Telefono, LENGTH(u.Telefono) - 3, 4)
		WHERE u.IdUsuario IS NULL
        ORDER BY IdMensajeExterno DESC
    )A ;
END $$
DELIMITER ;
