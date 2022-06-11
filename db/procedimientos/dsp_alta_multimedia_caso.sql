DROP PROCEDURE IF EXISTS `dsp_alta_multimedia_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_multimedia_caso`(pIdCaso int, pMultimedia json)
PROC: BEGIN
    DECLARE pIndice, pIdMultimedia, pIdCarpetaCaso int;
    DECLARE pURL, pNombre varchar(100);
    DECLARE pTipo, pOrigenMultimedia char(1);

    START TRANSACTION;
        SET pIndice = 0;
        WHILE pIndice < JSON_LENGTH(pMultimedia) DO
			SET pIdMultimedia = (SELECT COALESCE(MAX(IdMultimedia), 0) + 1 FROM Multimedia);
            SET pURL = JSON_UNQUOTE(JSON_EXTRACT(pMultimedia, CONCAT('$[', pIndice,'].URL')));
            SET pNombre = JSON_UNQUOTE(JSON_EXTRACT(pMultimedia, CONCAT('$[', pIndice,'].Nombre')));
            SET pTipo = JSON_UNQUOTE(JSON_EXTRACT(pMultimedia, CONCAT('$[', pIndice,'].Tipo')));
            SET pOrigenMultimedia = JSON_UNQUOTE(JSON_EXTRACT(pMultimedia, CONCAT('$[', pIndice,'].OrigenMultimedia')));
            SET pIdCarpetaCaso = JSON_UNQUOTE(JSON_EXTRACT(pMultimedia, CONCAT('$[', pIndice,'].IdCarpetaCaso')));
			
            INSERT INTO Multimedia VALUES (pIdMultimedia, pURL, NOW(), pTipo, pNombre);
            
            INSERT INTO MultimediaCaso VALUES (pIdMultimedia, pIdCaso, pOrigenMultimedia);

            IF COALESCE(pIdCarpetaCaso, 0) != 0 THEN
                INSERT INTO MultimediaCarpeta VALUES (pIdMultimedia, pIdCarpetaCaso);
            END IF;
            
			SET pIndice = pIndice + 1;	
        END WHILE;
        
        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
