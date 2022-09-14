DROP PROCEDURE IF EXISTS `dsp_listar_chats_externo`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_chats_externo`()
PROC: BEGIN
    DROP TEMPORARY TABLE IF EXISTS AUX;
    CREATE TEMPORARY TABLE AUX
    SELECT mx.IdChatApi, mx.FechaEnviado
    FROM MensajesExterno mx
    LEFT JOIN Usuarios u ON SUBSTRING(mx.IdChatApi, LENGTH(mx.IdChatApi) - 8, 4) = SUBSTRING(u.Telefono, LENGTH(u.Telefono) - 3, 4)
    WHERE u.IdUsuario IS NULL
    ORDER BY IdMensajeExterno DESC;
        
    SELECT IdChatApi, FechaEnviado Fecha FROM AUX GROUP BY 1;
    DROP TEMPORARY TABLE IF EXISTS AUX;
END $$
DELIMITER ;
