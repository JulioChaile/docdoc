
DROP PROCEDURE IF EXISTS `dsp_listar_multimedia_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_multimedia_caso`(pIdCaso bigint)
BEGIN
	/*
    Permite listar los archivos de un caso.
    */
    SELECT      *
    FROM        Multimedia m
    INNER JOIN  MultimediaCaso mc USING (IdMultimedia)
    LEFT JOIN   MultimediaCarpeta mca USING (IdMultimedia)
    WHERE       IdCaso = pIdCaso OR pIdCaso = 0;
END $$
DELIMITER ;
