DROP PROCEDURE IF EXISTS `dsp_duplicar_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_duplicar_caso`(pIdCaso bigint, pIdEstudio int)
PROC: BEGIN
	/*
    Permite dar de alta una consulta desde la web. 
    Devuelve OK + id de la consulta creada o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioCaso int;
    DECLARE pIdCasoNew, pIdCasoEstudio bigint;
    START TRANSACTION;
        SET pIdCasoNew = (SELECT COALESCE(MAX(IdCaso),0) + 1 FROM Casos);
        
        INSERT INTO Casos SELECT pIdCasoNew, IdJuzgado, IdNominacion, IdCompetencia, IdTipoCaso, IdEstadoCaso, IdEstadoAmbitoGestion, IdOrigen, Caratula, NroExpediente, FechaEstado, Carpeta, NOW(), Observaciones, FechaUltVisita, Estado FROM Casos WHERE IdCaso = pIdCaso;
		
        SET pIdUsuarioCaso = (SELECT COALESCE(MAX(IdUsuarioCaso),0) FROM UsuariosCaso);
        
        SET @a = pIdUsuarioCaso;
        
        INSERT IGNORE INTO UsuariosCaso
        SELECT 	@a := @a + 1, pIdCasoNew, pIdEstudio, IdUsuario, 'A', 'S'
        FROM 	UsuariosEstudio WHERE IdEstudio = pIdEstudio;

        INSERT INTO PersonasCaso SELECT pIdCasoNew, IdPersona, IdRTC, EsPrincipal, Observaciones, ValoresParametros, DocumentacionSolicitada FROM PersonasCaso WHERE IdCaso = pIdCaso;

        INSERT INTO ParametrosCaso SELECT pIdCasoNew, Parametros FROM ParametrosCaso WHERE IdCaso = pIdCaso;

        INSERT INTO MultimediaCaso SELECT IdMultimedia, pIdCasoNew, OrigenMultimedia FROM MultimediaCaso WHERE IdCaso = pIdCaso;

        -- Ingreso objetivos por defecto
        INSERT IGNORE INTO Objetivos
        SELECT @o := @o + 1, pIdCasoNew, oe.ObjetivoEstudio, NOW()
        FROM ObjetivosEstudio oe, (SELECT @o := MAX(IdObjetivo) FROM Objetivos) s
        WHERE oe.IdEstudio = pIdEstudio;

        IF EXISTS (SELECT 1 FROM IdsCasosEstudio WHERE IdEstudio = pIdEstudio) THEN
            SET pIdCasoEstudio = (SELECT MAX(IdCasoEstudio) + 1 FROM IdsCasosEstudio WHERE IdEstudio = pIdEstudio);
        ELSE
            SET pIdCasoEstudio = 1;
        END IF;

        INSERT INTO IdsCasosEstudio SELECT pIdCasoEstudio, pIdCasoNew, pIdEstudio;
        
        SELECT CONCAT('OK', pIdCasoNew);
	COMMIT;
END $$
DELIMITER ;
