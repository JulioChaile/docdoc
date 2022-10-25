DROP PROCEDURE IF EXISTS `dsp_alta_recordatorio_mov`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_recordatorio_mov`(pIdMovimientoCaso bigint, pFrecuencia int)
PROC: BEGIN
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
  START TRANSACTION;
    IF EXISTS (SELECT 1 FROM RecordatorioMovimiento WHERE IdMovimientoCaso = pIdMovimientoCaso) THEN
      UPDATE  RecordatorioMovimiento
      SET     Frecuencia = pFrecuencia,
              UltimoRecordatorio = DATE(NOW())
      WHERE IdMovimientoCaso = pIdMovimientoCaso;

    ELSE
      INSERT INTO RecordatorioMovimiento (IdMovimientoCaso, Frecuencia, UltimoRecordatorio) VALUES (pIdMovimientoCaso, pFrecuencia, DATE(NOW()));
    END IF;
        
    SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
