DROP PROCEDURE IF EXISTS `dsp_alta_personas_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_personas_caso`(pJWT varchar(500), pIdCaso bigint, pPersonasCaso json, 
			pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite dar de alta personas y asignarlas a un caso. Se controla si la persona existe en el estudio y si existe,
    no se modifican sus datos personales. Devuelve OK o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion int;
    DECLARE pMensaje varchar(100);
    
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
	START TRANSACTION;
		CALL dsp_alta_personas_caso_comun(pJWT, pIdCaso, pPersonasCaso, pIP, pUserAgent, pApp, pMensaje);
        
		SELECT pMensaje Mensaje;
	COMMIT;
END $$
DELIMITER ;
