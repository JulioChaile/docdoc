DROP PROCEDURE IF EXISTS `dsp_modificar_plantilla_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_plantilla_estudio`(pIdEstudio int, pIdPlantilla int, pNombre varchar(45), pPlantilla longtext, pActores int, pDemandados int)
PROC: BEGIN
	-- Control de parámetros vacíos
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio jurídico.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdPlantilla IS NULL THEN
		SELECT 'Debe indicar una plantilla.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pNombre IS NULL OR pNombre = '' THEN
		SELECT 'Debe indicar un nombre para la plantilla.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pPlantilla IS NULL OR pPlantilla = '' THEN
		SELECT 'La plantilla debe tener algun contenido.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pActores IS NULL OR pActores = 0 THEN
		SELECT 'Debe indicar el numero de actores.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pDemandados IS NULL OR pDemandados = 0 THEN
		SELECT 'Debe indicar el numero de demandados.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM PlantillasEstudio WHERE IdPlantilla = pIdPlantilla) THEN
		SELECT 'La plantilla indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT 1 FROM PlantillasEstudio WHERE Nombre = pNombre AND IdEstudio = pIdEstudio AND IdPlantilla != pIdPlantilla) THEN
		SELECT 'Ya existe una plantilla con ese nombre.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		UPDATE  PlantillasEstudio
        SET     Nombre = pNombre,
                Plantilla = pPlantilla,
                Actores = pActores,
                Demandados = pDemandados
        WHERE   IdPlantilla = pIdPlantilla;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
