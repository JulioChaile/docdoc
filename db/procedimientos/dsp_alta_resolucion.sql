DROP PROCEDURE IF EXISTS `dsp_alta_resolucion`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_resolucion`(pResolucion varchar(500), pFecha datetime, pMonto bigint)
PROC: BEGIN
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    START TRANSACTION;
        INSERT INTO ResolucionesSMVM SELECT 0, pResolucion, pFecha, pMonto;
        
        SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
	COMMIT;
END $$
DELIMITER ;
