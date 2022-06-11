DROP PROCEDURE IF EXISTS `dsp_dame_chat`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_chat`(pIdChat bigint, pIdExternoChat varchar(64))
PROC: BEGIN

    DECLARE pMensaje varchar(1000);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    -- Control de parametros
    IF NOT EXISTS (SELECT * FROM Chats WHERE IdExternoChat = COALESCE(pIdExternoChat, 0) OR IdChat = COALESCE(pIdChat, 0)) THEN
        SELECT 'Error, el chat no existe en la base de datos.' Mensaje;
        LEAVE PROC;
    END IF;
    SELECT ct.*, (SELECT	JSON_ARRAYAGG(ua.TokenApp)
                  FROM  UsuariosCaso uc
                  INNER JOIN UsuariosTokenApp ua USING(IdUsuario)
                  WHERE uc.IdCaso = ct.IdCaso) Tokens
    FROM Chats ct
    WHERE ct.IdExternoChat = COALESCE(pIdExternoChat, 0) OR ct.IdChat = COALESCE(pIdChat, 0);
END $$
DELIMITER ;
