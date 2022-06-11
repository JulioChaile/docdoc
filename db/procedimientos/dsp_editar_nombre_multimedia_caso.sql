DROP PROCEDURE IF EXISTS `dsp_editar_nombre_multimedia_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_editar_nombre_multimedia_caso`(pIdCaso int, pIdMultimedia int, pNombre varchar(100))
PROC: BEGIN
	-- Control de parámetros vacíos
    IF pIdMultimedia IS NULL OR pIdMultimedia = '' THEN
		SELECT 'Debe indicar un archivo.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pIdCaso IS NULL OR pIdCaso = '' THEN
		SELECT 'Debe indicar un caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pNombre IS NULL OR pNombre = '' THEN
		SELECT 'Debe indicar un nombre para el archivo.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT 1 FROM Multimedia WHERE IdMultimedia = pIdMultimedia) THEN
		SELECT 'El archivo indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM MultimediaCaso WHERE IdMultimedia = pIdMultimedia AND IdCaso = pIdCaso) THEN
		SELECT 'El archivo indicado no pertenece a este caso.' Mensaje;
        LEAVE PROC;
	END IF;
    IF NOT EXISTS (SELECT 1 FROM Casos WHERE IdCaso = pIdCaso) THEN
		SELECT 'El caso indicado no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;        
		UPDATE  Multimedia
        SET     Nombre = pNombre
        WHERE   IdMultimedia = pIdMultimedia;
       
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
