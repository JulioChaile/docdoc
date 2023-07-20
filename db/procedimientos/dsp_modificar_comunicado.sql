DROP PROCEDURE IF EXISTS `dsp_modificar_comunicado`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_comunicado`(pIdComunicado int, pTitulo varchar(100), pContenido text)
PROC: BEGIN

    -- Declarar variables para manejo de errores y mensajes
    DECLARE pMensaje varchar(1000);
    DECLARE errorOccurred BOOLEAN DEFAULT FALSE;
    DECLARE errorMessage VARCHAR(1000);
    
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        SET errorOccurred = TRUE;
        GET DIAGNOSTICS CONDITION 1 errorMessage = MESSAGE_TEXT;
        ROLLBACK;
        SELECT CONCAT('Error: ', errorMessage) AS Mensaje;
    END;
    START TRANSACTION;
      UPDATE Comunicados
      SET   Titulo = pTitulo,
            Contenido = pContenido
      WHERE IdComunicado = pIdComunicado;

        SELECT 'OK' Mensaje;
    COMMIT;
END $$
DELIMITER ;
