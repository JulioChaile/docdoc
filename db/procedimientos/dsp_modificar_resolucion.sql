DROP PROCEDURE IF EXISTS `dsp_modificar_resolucion`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_resolucion`(pIdResolucion int, pResolucion VARCHAR(500), pFecha DATETIME, pMonto bigint)
PROC: BEGIN
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    START TRANSACTION;
		UPDATE	ResolucionesSMVM
        SET		Resolucion = pResolucion,
                FechaResolucion = pFecha,
                MontoResolucion = pMonto
        WHERE	IdResolucionSMVM = pIdResolucion;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
