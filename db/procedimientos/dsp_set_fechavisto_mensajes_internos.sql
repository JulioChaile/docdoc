DROP PROCEDURE IF EXISTS `dsp_set_fechavisto_mensajes_internos`;
DELIMITER $$
CREATE PROCEDURE `dsp_set_fechavisto_mensajes_internos`(pIdCaso int, pCliente char(1))
PROC: BEGIN
    UPDATE  MensajesChatInterno
    SET     FechaVisto = NOW()
    WHERE   IdCaso = pIdCaso
            AND Cliente = pCliente
            AND FechaVisto IS NULL;

    SELECT CONCAT('OK');
END $$
DELIMITER ;0
