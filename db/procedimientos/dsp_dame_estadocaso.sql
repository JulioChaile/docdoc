DROP PROCEDURE IF EXISTS `dsp_dame_estadocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_estadocaso`(pIdEstadoCaso int)
BEGIN
	/*
    Permite instanciar un estado de caso de un estudio desde la base de datos.
    */
    SELECT 	*
    FROM	EstadosCaso
    WHERE	IdEstadoCaso = pIdEstadoCaso;
END $$
DELIMITER ;
