DROP PROCEDURE IF EXISTS `dsp_dame_estadocasospendientes`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_estadocasospendientes`(pIdEstadoCasoPendiente int)
BEGIN
	/*
    Permite instanciar una estado de ambito de gestion desde la base de datos.
    */
    SELECT	    e.*
    FROM	    EstadosCasoPendiente e
    WHERE	    e.IdEstadoCasoPendiente = pIdEstadoCasoPendiente;
END $$
DELIMITER ;
