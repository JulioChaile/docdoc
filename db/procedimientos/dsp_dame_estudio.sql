DROP PROCEDURE IF EXISTS `dsp_dame_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_estudio`(pIdEstudio int)
BEGIN
	/*
    Permite instanciar un Estudio desde la base de datos.
    */
    SELECT		e.*, c.IdProvincia, p.Provincia
    FROM		Estudios e
    INNER JOIN	Ciudades c USING (IdCiudad)
    INNER JOIN	Provincias p USING (IdProvincia)
    WHERE		IdEstudio = pIdEstudio;
END $$
DELIMITER ;
