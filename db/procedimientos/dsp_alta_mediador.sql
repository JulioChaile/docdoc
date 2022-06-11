DROP PROCEDURE IF EXISTS `dsp_alta_mediador`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_mediador`(pJWT varchar(500), pNombre varchar(200), pRegistro tinyint(4), pMP varchar(500), pDomicilio varchar(200), pTelefono varchar(200), pEmail varchar(50),
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite crear mediadores controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + Id del mediador creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdMediador int;
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
    IF pNombre IS NULL OR pNombre = '' THEN
		SELECT 'Debe indicar el nombre del mediador.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT IdMediador FROM Mediadores WHERE Nombre = pNombre) THEN
		SELECT 'El mediador indicado ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;        
        INSERT INTO Mediadores SELECT 0, pRegistro, pNombre, pMP, pDomicilio, pTelefono, pEmail;
        SET pIdMediador = LAST_INSERT_ID();
        
        SELECT CONCAT('OK', pIdMediador) Mensaje;
	COMMIT;
END $$
DELIMITER ;
