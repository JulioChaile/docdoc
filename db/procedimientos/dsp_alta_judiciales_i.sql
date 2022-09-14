set collation_connection=utf8mb4_unicode_ci;
DROP PROCEDURE IF EXISTS `dsp_alta_judiciales_i`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_judiciales_i`(pIdJudicialesC int, pIdCaso int)
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parámetros vacíos
    IF pIdJudicialesC IS NULL THEN
        SELECT 'Debe indicar un usuario.' Mensaje;
        LEAVE PROC;
    END IF;
    IF pIdCaso IS NULL THEN
        SELECT 'Debe indicar un estado.' Mensaje;
        LEAVE PROC;
    END IF;
    START TRANSACTION;
            INSERT INTO JudicialesI (IdJudicialesC, IdCaso)
            VALUES (pIdJudicialesC, pIdCaso);

            SELECT 'OK';
    COMMIT;
END $$
DELIMITER ;
