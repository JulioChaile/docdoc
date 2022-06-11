DROP PROCEDURE IF EXISTS `dsp_modificar_carpeta_plantilla_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_carpeta_plantilla_estudio`(pIdEstudio int, pIdCarpetaPlantilla int, pNombre varchar(45))
PROC: BEGIN
	-- Control de parámetros vacíos
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio jurídico.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdCarpetaPlantilla IS NULL THEN
		SELECT 'Debe indicar una carpeta.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pNombre IS NULL OR pNombre = '' THEN
		SELECT 'Debe indicar un nombre para la plantilla.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM CarpetasPlantillasEstudio WHERE IdCarpetaPlantilla = pIdCarpetaPlantilla) THEN
		SELECT 'La carpeta indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		UPDATE  CarpetasPlantillasEstudio
        SET     Nombre = pNombre
        WHERE   IdCarpetaPlantilla = pIdCarpetaPlantilla;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
