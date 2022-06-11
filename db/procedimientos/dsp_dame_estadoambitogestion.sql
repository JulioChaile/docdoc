DROP PROCEDURE IF EXISTS `dsp_dame_estadoambitogestion`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_estadoambitogestion`(pIdEstadoAmbitoGestion smallint)
BEGIN
	/*
    Permite instanciar una estado de ambito de gestion desde la base de datos.
    */
    SELECT	    e.*
    FROM	    EstadoAmbitoGestion e
    WHERE	    e.IdEstadoAmbitoGestion = pIdEstadoAmbitoGestion;
END $$
DELIMITER ;
