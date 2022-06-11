DROP PROCEDURE IF EXISTS `dsp_alta_competencia`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_competencia`(pJWT varchar(500), pCompetencia varchar(255),
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear competencias controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + Id del competencia creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdCompetencia int;
    DECLARE pIdUsuarioGestion int;
    DECLARE pUsuario varchar(120);
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
	-- Validación de sesión
    SET pIdUsuarioGestion = f_valida_sesion_usuario(pJWT);
    IF pIdUsuarioGestion = 0 THEN
		SELECT 'Ocurrió un problema con su sesión.' Mensaje;
        LEAVE PROC;
	END IF;
	-- Control de parámetros vacíos
    IF pCompetencia IS NULL OR pCompetencia = '' THEN
		SELECT 'Debe indicar el nombre del competencia.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT IdCompetencia FROM Competencias WHERE Competencia = pCompetencia) THEN
		SELECT 'El competencia indicado ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        
        INSERT INTO Competencias SELECT 0, pCompetencia, 'A';
        SET pIdCompetencia = LAST_INSERT_ID();
        
        SELECT CONCAT('OK', pIdCompetencia) Mensaje;
	COMMIT;
END $$
DELIMITER ;
