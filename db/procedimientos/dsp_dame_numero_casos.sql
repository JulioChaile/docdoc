DROP PROCEDURE IF EXISTS `dsp_dame_numero_casos`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_numero_casos`(pIdEstudio int)
BEGIN    
    DROP TEMPORARY TABLE IF EXISTS tmp_casos_estudio;
    CREATE TEMPORARY TABLE tmp_casos_estudio ENGINE=MEMORY
        SELECT DISTINCT	uc.IdCaso, t.TipoCaso
        FROM 			UsuariosCaso uc
        INNER JOIN		Casos c USING (IdCaso)
        INNER JOIN		TiposCaso t USING (IdTipoCaso)
        WHERE 			uc.IdEstudio = pIdEstudio;
        
    SELECT 	    TipoCaso TipoCaso, COUNT(TipoCaso) Cantidad
    FROM 	    tmp_casos_estudio
    GROUP BY    TipoCaso;

    DROP TEMPORARY TABLE IF EXISTS tmp_casos_estudio;
END $$
DELIMITER ;
