DROP PROCEDURE IF EXISTS `dsp_dame_parametro`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_parametro`(pParametro varchar(20))
BEGIN
	/*
    Permite instanciar un parámetro de empresa desde la base de datos.
    */
    
    SELECT	*
    FROM	Empresa
    WHERE	Parametro = pParametro;
END $$
DELIMITER ;
