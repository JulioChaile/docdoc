DROP PROCEDURE IF EXISTS `dsp_alta_rol`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_rol`(pJWT varchar(500), pRol varchar(30), 
				pObservaciones varchar(255), pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
SALIR: BEGIN
	/*
    Permite dar de alta un Rol controlando que el nombre del rol no exista ya. 
    Devuelve OK + Id o el mensaje de error en Mensaje.
    */
    DECLARE pIdRol int;
    DECLARE pIdUsuario int;
	DECLARE pUsuario varchar(120);
    -- Manejo de error en la transacción    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
        ROLLBACK;
	END;
    -- Controla Parámetros.
    SET pIdUsuario = f_valida_sesion_usuario(pJWT);
    IF pIdUsuario = 0 THEN
		SELECT 'La sesión expiró. Vuelva a iniciar sesión.' Mensaje;
        LEAVE SALIR;
	END IF;
	IF EXISTS(SELECT Rol FROM Roles WHERE Rol = pRol) THEN
		SELECT 'El nombre del rol ya existe.' Mensaje;
		LEAVE SALIR;
	END IF;
    IF (pRol IS NULL OR pRol = '') THEN
        SELECT 'Debe ingresar el nombre del rol.' Mensaje;
        LEAVE SALIR;
	END IF;
	-- Da de alta calculando el próximo id
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuario);
		SET pIdRol = (SELECT COALESCE(MAX(IdRol), 0) + 1 FROM Roles);
        
        INSERT INTO Roles VALUES (pIdRol, pRol, 'A', pObservaciones);
		
        -- Audito
		INSERT INTO aud_Roles
		SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA', 'I', Roles.* 
        FROM Roles WHERE IdRol = pIdRol;
        SELECT CONCAT('OK', pIdRol) Mensaje;
	COMMIT;
END $$
DELIMITER ;
