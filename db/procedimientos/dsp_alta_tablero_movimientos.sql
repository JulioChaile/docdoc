DROP PROCEDURE IF EXISTS `dsp_alta_tablero_movimientos`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_tablero_movimientos`(pJWT varchar(500), pIdTipoMov int, pOrden int, pIdEstudio int, pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear un origen de caso controlando que no exista ya en el estudio indicado.
    Devuelve OK + Id del origen creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdOrigen int;
    DECLARE pUsuario varchar(120);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
	-- Validación de sesión
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'Ocurrió un problema con su sesión.' Mensaje;
        LEAVE PROC;
	END IF;
  START TRANSACTION;
        UPDATE TiposMovimientoTableros SET Orden = Orden + 1 WHERE Orden >= pOrden AND IdEstudio = pIdEstudio;

        INSERT INTO TiposMovimientoTableros VALUES(0, pIdTipoMov, pIdEstudio, pOrden);
        
        SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
	COMMIT;
END $$
DELIMITER ;
