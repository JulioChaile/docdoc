DROP PROCEDURE IF EXISTS `dsp_borrar_resolucion`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_resolucion`(pIdResolucion int)
PROC: BEGIN
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION  
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    START TRANSACTION;
		DELETE FROM ResolucionesSMVM WHERE	IdResolucionSMVM = pIdResolucion;
       
    SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
