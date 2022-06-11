DROP PROCEDURE IF EXISTS `dsp_dame_cia_seguro`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_cia_seguro`(pIdCiaSeguro int)
BEGIN
	/*
    Permite instanciar una campaña de difusión desde la base de datos.
    */
    SELECT	*
	FROM	CiasSeguro
    WHERE	IdCiaSeguro = pIdCiaSeguro;
END $$
DELIMITER ;
