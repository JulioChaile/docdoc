DROP PROCEDURE IF EXISTS `dsp_eliminar_plantilla_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_eliminar_plantilla_estudio`(pIdPlantilla int)
PROC: BEGIN
    START TRANSACTION;
        DELETE FROM PlantillasEstudio WHERE IdPlantilla = pIdPlantilla;
        
        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
