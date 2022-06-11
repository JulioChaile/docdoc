DROP PROCEDURE IF EXISTS `dsp_alta_multimedia_contacto`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_multimedia_contacto`(pIdContacto int, pMultimedia json)
PROC: BEGIN
    DECLARE pIndice, pIdMultimedia int;
    DECLARE pURL varchar(100);
    DECLARE pTipo char(1);

    START TRANSACTION;
        SET pIndice = 0;
        WHILE pIndice < JSON_LENGTH(pMultimedia) DO
			SET pIdMultimedia = (SELECT COALESCE(MAX(IdMultimedia), 0) + 1 FROM Multimedia);
            SET pURL = JSON_UNQUOTE(JSON_EXTRACT(pMultimedia, CONCAT('$[', pIndice,'].URL')));
            SET pTipo = JSON_UNQUOTE(JSON_EXTRACT(pMultimedia, CONCAT('$[', pIndice,'].Tipo')));
			
            INSERT INTO Multimedia VALUES (pIdMultimedia, pURL, NOW(), pTipo, pURL);
            
            INSERT INTO MultimediaContacto VALUES (pIdMultimedia, pIdContacto);
            
			SET pIndice = pIndice + 1;	
        END WHILE;
        
        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
