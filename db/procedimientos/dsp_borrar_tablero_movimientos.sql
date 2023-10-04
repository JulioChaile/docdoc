DROP PROCEDURE IF EXISTS `dsp_borrar_tablero_movimientos`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_tablero_movimientos`(pJWT varchar(500), pIdTipoMovimientoTablero int, pIdEstudio int,
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite borrar un origen controlando que no existan casos asociados. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pUsuario varchar(120);
    DECLARE pOrden int(2);
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
    SET pOrden = (SELECT Orden FROM TiposMovimientoTableros WHERE IdTipoMovimientoTablero = pIdTipoMovimientoTablero);
    START TRANSACTION;			
        DELETE FROM TiposMovimientoTableros WHERE IdTipoMovimientoTablero = pIdTipoMovimientoTablero;

        UPDATE TiposMovimientoTableros SET Orden = Orden - 1 WHERE Orden > pOrden AND IdEstudio = pIdEstudio;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
