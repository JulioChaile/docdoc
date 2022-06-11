DROP PROCEDURE IF EXISTS `dsp_dame_tipocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_tipocaso`(pIdTipoCaso smallint)
BEGIN
	/*
    Permite instanciar un tipo de caso desde la base de datos.
    */
    SELECT	*
    FROM	TiposCaso
    WHERE	IdTipoCaso = pIdTipoCaso;
END $$
DELIMITER ;
