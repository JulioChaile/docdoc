DROP PROCEDURE IF EXISTS `dsp_eliminar_multimedia_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_eliminar_multimedia_caso`(pIdCaso int, pMultimedia json)
PROC: BEGIN
    DECLARE pIndice, pIdMultimedia int;
    DECLARE pOrigenMultimedia char(1);

    START TRANSACTION;
        SET pIndice = 0;
        WHILE pIndice < JSON_LENGTH(pMultimedia) DO
			SET pIdMultimedia = JSON_UNQUOTE(JSON_EXTRACT(pMultimedia, CONCAT('$[', pIndice,'].IdMultimedia')));
            SET pOrigenMultimedia = JSON_UNQUOTE(JSON_EXTRACT(pMultimedia, CONCAT('$[', pIndice,'].OrigenMultimedia')));
            
            IF pOrigenMultimedia = 'M' THEN
                DELETE FROM MultimediaMovimiento WHERE IdMultimedia = pIdMultimedia;
            END IF;

            DELETE FROM MultimediaCaso WHERE IdMultimedia = pIdMultimedia;

            DELETE FROM MultimediaCarpeta WHERE IdMultimedia = pIdMultimedia;

            DELETE FROM Multimedia WHERE IdMultimedia = pIdMultimedia;
            
			SET pIndice = pIndice + 1;	
        END WHILE;
        
        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
