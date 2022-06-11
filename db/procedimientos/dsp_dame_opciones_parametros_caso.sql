DROP PROCEDURE IF EXISTS `dsp_dame_opciones_parametros_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_opciones_parametros_caso`()
BEGIN
    SELECT * FROM OpcionesParametrosCaso;
END $$
DELIMITER ;
