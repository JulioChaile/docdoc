DROP PROCEDURE IF EXISTS `dsp_alta_carpeta_plantilla_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_carpeta_plantilla_estudio`(pIdEstudio int, pNombre varchar(45), pIdCarpetaPadre int)
PROC: BEGIN
	-- Control de parámetros vacíos
  IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio jurídico.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pNombre IS NULL OR pNombre = '' THEN
		SELECT 'Debe indicar un nombre.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdEstudio FROM Estudios WHERE IdEstudio = pIdEstudio) THEN
		SELECT 'El estudio indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		INSERT INTO CarpetasPlantillasEstudio VALUES (0, pIdEstudio, pNombre, COALESCE(pIdCarpetaPadre, NULL));
        
    SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
	COMMIT;
END $$
DELIMITER ;
