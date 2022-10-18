DROP PROCEDURE IF EXISTS `dsp_alta_recordatorio_doc`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_recordatorio_doc`(pIdCaso bigint, pFechaLimite date, pFrecuencia int)
PROC: BEGIN
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
  START TRANSACTION;
    IF EXISTS (SELECT 1 FROM RecordatorioDocumentacion WHERE IdCaso = pIdCaso) THEN
      UPDATE  RecordatorioDocumentacion
      SET     FechaLimite = pFechaLimite,
              Frecuencia = pFrecuencia,
              UltimoRecordatorio = DATE(NOW()),
              Activa = 'S'
      WHERE IdCaso = pIdCaso;

    ELSE
      INSERT INTO RecordatorioDocumentacion (IdCaso, FechaLimite, Frecuencia, Activa, UltimoRecordatorio) VALUES (pIdCaso, pFechaLimite, pFrecuencia, 'S', DATE(NOW()));
    END IF;
        
    SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
