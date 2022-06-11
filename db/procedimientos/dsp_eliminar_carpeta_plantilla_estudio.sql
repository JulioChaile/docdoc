DROP PROCEDURE IF EXISTS `dsp_eliminar_carpeta_plantilla_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_eliminar_carpeta_plantilla_estudio`(pIdCarpetaPlantilla int)
PROC: BEGIN
    START TRANSACTION;
        UPDATE  CarpetasPlantillasEstudio
        SET     IdCarpetaPadre = NULL
        WHERE   IdCarpetaPadre = pIdCarpetaPlantilla;

        UPDATE  PlantillasEstudio
        SET     IdCarpetaPadre = NULL
        WHERE   IdCarpetaPadre = pIdCarpetaPlantilla;
        
        DELETE FROM CarpetasPlantillasEstudio WHERE IdCarpetaPlantilla = pIdCarpetaPlantilla;
        
        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
