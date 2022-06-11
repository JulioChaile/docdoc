DROP PROCEDURE IF EXISTS `dsp_borrar_estado_juzgado`;
DELIMITER $$
CREATE PROCEDURE `dsp_borrar_estado_juzgado`(pJWT varchar(500), pIdEstadoAmbitoGestion smallint, pIdJuzgado int, 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
    /*
    Permite borrar un tipo de caso de una Juzgado controlando que no tenga roles asociados. 
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

    SET pOrden = (SELECT Orden FROM JuzgadosEstadosAmbitos WHERE IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion AND IdJuzgado = pIdJuzgado);
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
    IF NOT EXISTS (SELECT IdJuzgado FROM Juzgados WHERE IdJuzgado = pIdJuzgado) THEN
		SELECT 'El juzgado indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdEstadoAmbitoGestion, IdJuzgado FROM Casos WHERE IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion AND IdJuzgado = pIdJuzgado) THEN
		SELECT 'No se puede borrar el estado. Existen casos asociados.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT IdEstadoAmbitoGestion, IdJuzgado FROM JuzgadosEstadosAmbitos WHERE IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion AND IdJuzgado = pIdJuzgado) THEN
		SELECT 'El estado no existe en el juzgado asociado.' Mensaje;
        LEAVE PROC;
	END IF;

    START TRANSACTION;
        
		DELETE FROM JuzgadosEstadosAmbitos WHERE IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion AND IdJuzgado = pIdJuzgado;

        UPDATE JuzgadosEstadosAmbitos SET Orden = Orden - 1 WHERE Orden > pOrden AND IdJuzgado = pIdJuzgado;
       
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
