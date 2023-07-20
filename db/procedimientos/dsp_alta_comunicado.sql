DROP PROCEDURE IF EXISTS `dsp_alta_comunicado`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_comunicado`(pTitulo varchar(100), pContenido text, pIdMultimedia int, pFechaComunicado date, pIdEstudio int)
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    START TRANSACTION;
            INSERT INTO Comunicados (Titulo, Contenido, IdMultimedia, FechaComunicado, IdEstudio, FechaAlta)
            VALUES (pTitulo, pContenido, pIdMultimedia, NOW(), pIdEstudio, NOW());

            SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
    COMMIT;
END $$
DELIMITER ;
