DROP PROCEDURE IF EXISTS `dsp_alta_documentacion_solicitada`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_documentacion_solicitada`(pDoc varchar(100))
PROC: BEGIN
    START TRANSACTION;
        INSERT INTO DocumentacionParaSolicitar SELECT 0, pDoc;

        SELECT 'OK';
	COMMIT;
END $$
DELIMITER ;
