DROP PROCEDURE IF EXISTS `dsp_editar_estadoambitogestion_juzgado`;
DELIMITER $$
CREATE PROCEDURE `dsp_editar_estadoambitogestion_juzgado`(pJWT varchar(500), pIdEstadoAmbitoGestion int, pIdJuzgado smallint, pOrden int)
PROC: BEGIN
	/*
    Editar el orden del estado de un juzgado controlando que ambos existan.
    Devuelve OK o un mensaje de error en Mensaje. 
    */
    DECLARE pIdUsuarioGestion, pOrdenOld int;
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
	-- Control de parámetros vacíos
    IF pIdEstadoAmbitoGestion IS NULL OR pIdEstadoAmbitoGestion = '' THEN
		SELECT 'Debe indicar un estado.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdJuzgado IS NULL OR pIdJuzgado = '' THEN
		SELECT 'Debe indicar un juzgado.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstadoAmbitoGestion FROM EstadoAmbitoGestion WHERE IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion) THEN
		SELECT 'El estado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJuzgado = pIdJuzgado AND Estado = 'A') THEN
		SELECT 'El juzgado indicado no existe en el sistema o bien no está activo.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        SET pOrdenOld = (SELECT Orden FROM JuzgadosEstadosAmbitos WHERE IdJuzgado = pIdJuzgado AND IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion);

        IF (pOrdenOld > pOrden) THEN
            UPDATE  JuzgadosEstadosAmbitos
            SET     Orden = Orden + 1
            WHERE Orden >= pOrden AND Orden < pOrdenOld AND IdJuzgado = pIdJuzgado;
        ELSE
            UPDATE  JuzgadosEstadosAmbitos
            SET     Orden = Orden - 1
            WHERE Orden <= pOrden AND Orden > pOrdenOld AND IdJuzgado = pIdJuzgado;
        END IF;
        
		UPDATE  JuzgadosEstadosAmbitos
        SET     Orden = pOrden
        WHERE IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion AND IdJuzgado = pIdJuzgado;
       
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
