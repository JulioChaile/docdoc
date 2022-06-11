DROP PROCEDURE IF EXISTS `dsp_listar_documentacion_solicitada`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_documentacion_solicitada`()
PROC: BEGIN
    SELECT		Documentacion
    FROM		DocumentacionParaSolicitar;
END $$
DELIMITER ;
