DROP PROCEDURE IF EXISTS `dsp_aux_inserts`;
DELIMITER $$
CREATE PROCEDURE `dsp_aux_inserts`(pIdEstudio int, OUT pMensaje varchar(100))
PROC: BEGIN
	/*
    Procedimiento de uso interno que inserta los valores por defecto de las tablas siempre y cuando no exista ningún valor
    del estudio en dicha tabla:
		EstadosCaso, RolesEstudio, TiposMoviimiento y Parametros
    */
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
		BEGIN
			-- SHOW ERRORS;
			SET pMensaje = 'Error en la carga de valores por defecto para el estudio.';
        END;
        
    -- Estados Caso
    IF NOT EXISTS (SELECT IdEstadoCaso FROM EstadosCaso WHERE IdEstudio = pIdEstudio) THEN
		SET @IdEstadoCaso = (SELECT COALESCE(MAX(IdEstadoCaso),0) FROM EstadosCaso);
		
		INSERT INTO EstadosCaso VALUES (@IdEstadoCaso := @IdEstadoCaso + 1, pIdEstudio, 'Cliente pendiente', 'A');
		INSERT INTO EstadosCaso VALUES (@IdEstadoCaso := @IdEstadoCaso + 1, pIdEstudio, 'Llamar al cliente', 'A');
		INSERT INTO EstadosCaso VALUES (@IdEstadoCaso := @IdEstadoCaso + 1, pIdEstudio, 'Finalizado', 'A');
		INSERT INTO EstadosCaso VALUES (@IdEstadoCaso := @IdEstadoCaso + 1, pIdEstudio, 'Archivo', 'A');
		INSERT INTO EstadosCaso VALUES (@IdEstadoCaso := @IdEstadoCaso + 1, pIdEstudio, 'Judicial', 'A');
		INSERT INTO EstadosCaso VALUES (@IdEstadoCaso := @IdEstadoCaso + 1, pIdEstudio, 'Administrativo', 'A');
	END IF;

    -- RolesEstudio
    IF NOT EXISTS (SELECT IdRolEstudio FROM RolesEstudio WHERE IdEstudio = pIdEstudio) THEN
		SET @IdRolEstudio = (SELECT COALESCE(MAX(IdRolEstudio),0) FROM RolesEstudio);
		INSERT INTO RolesEstudio VALUES(@IdRolEstudio := @IdRolEstudio + 1, pIdEstudio, 'Abogado');
		INSERT INTO RolesEstudio VALUES(@IdRolEstudio := @IdRolEstudio + 1, pIdEstudio, 'Junior');
		INSERT INTO RolesEstudio VALUES(@IdRolEstudio := @IdRolEstudio + 1, pIdEstudio, 'Administrativo');
	END IF;
    
    -- TiposMovimiento
    IF NOT EXISTS (SELECT IdTipoMov FROM TiposMovimiento WHERE IdEstudio = pIdEstudio) THEN
		SET @IdTipoMov = (SELECT COALESCE(MAX(IdTipoMov),0) FROM TiposMovimiento);
		INSERT INTO TiposMovimiento VALUES(@IdTipoMov := @IdTipoMov + 1, pIdEstudio, 'Apertura a prueba', 'P');
		INSERT INTO TiposMovimiento VALUES(@IdTipoMov := @IdTipoMov + 1, pIdEstudio, 'Producción de prueba', 'P');
		INSERT INTO TiposMovimiento VALUES(@IdTipoMov := @IdTipoMov + 1, pIdEstudio, 'Traslado de demanda', 'P');
		INSERT INTO TiposMovimiento VALUES(@IdTipoMov := @IdTipoMov + 1, pIdEstudio, 'Contesta demanda', 'P');
		INSERT INTO TiposMovimiento VALUES(@IdTipoMov := @IdTipoMov + 1, pIdEstudio, 'Alegatos', 'P');
		INSERT INTO TiposMovimiento VALUES(@IdTipoMov := @IdTipoMov + 1, pIdEstudio, 'Gestión oficina', 'O');
		INSERT INTO TiposMovimiento VALUES(@IdTipoMov := @IdTipoMov + 1, pIdEstudio, 'Inicia Demanda', 'P');
    END IF;

    SET pMensaje = 'OK';
END $$
DELIMITER ;
