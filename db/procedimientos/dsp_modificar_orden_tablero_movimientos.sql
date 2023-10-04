DROP PROCEDURE IF EXISTS `dsp_modificar_orden_tablero_movimientos`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_orden_tablero_movimientos`(pJWT varchar(500), pIdTipoMovimientoTablero int, pIdEstudio int, pOrden int,
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite modificar un origen de casos controlando que el usuario que ejecuta la acción pertence al estudio
    al cual pertenece el origen indicado.
    */
    DECLARE pIdUsuarioGestion, pOrdenOld int;
    DECLARE pUsuario varchar(120);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			SHOW ERRORS;
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
    SET pOrdenOld = (SELECT Orden FROM TiposMovimientoTableros WHERE IdTipoMovimientoTablero = pIdTipoMovimientoTablero);

        IF (pOrdenOld > pOrden) THEN
            UPDATE  TiposMovimientoTableros
            SET     Orden = Orden + 1
            WHERE Orden >= pOrden AND Orden < pOrdenOld AND IdEstudio = pIdEstudio;
        ELSE
            UPDATE  TiposMovimientoTableros
            SET     Orden = Orden - 1
            WHERE Orden <= pOrden AND Orden > pOrdenOld AND IdEstudio = pIdEstudio;
        END IF;
        
        UPDATE	TiposMovimientoTableros
        SET		Orden = pOrden
		WHERE	IdTipoMovimientoTablero = pIdTipoMovimientoTablero;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
