DROP PROCEDURE IF EXISTS `dsp_borrar_comunicado`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_comunicado`(pIdComunicado int)
PROC: BEGIN
	DECLARE pIdUsuarioGestion int;
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    START TRANSACTION;
		    DELETE FROM Comunicados WHERE IdComunicado = pIdComunicado;

        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
