DROP PROCEDURE IF EXISTS `dsp_dame_datos_empresa`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_datos_empresa`()
BEGIN
	/*
    Permite traer en formato resultset los parámetros de la 
    empresa que necesitan cargarse al inicio de sesión (EsInicial = S).
    */
    SELECT	*
    FROM	Empresa
    WHERE	EsInicial = 'S';
END $$
DELIMITER ;
