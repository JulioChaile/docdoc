DROP PROCEDURE IF EXISTS `dsp_alta_rol_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_rol_estudio`(pJWT varchar(500), pIdEstudio int, pRolEstudio varchar(50), 
		pIP varchar(40), pUserAgent varchar(255), pApp varchar(50))
PROC: BEGIN
	/*
    Permite dar de alta roles en un estudio siempre que no exista uno con el mismo nombre.
    Devuelve OK + Id del rol de estudio creado o un mensaje de error en Mensaje.
    */
    DECLARE pIdUsuarioGestion, pIdRolEstudio int;
    DECLARE pUsuario varchar(120);
    -- Manejo de errores en la transacción
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
    IF pIdEstudio IS NULL THEN
		SELECT 'Debe indicar un estudio.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pRolEstudio IS NULL THEN
		SELECT 'Debe indicar el nombre del rol.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT RolEstudio FROM RolesEstudio WHERE IdEstudio = pIdEstudio AND RolEstudio = pRolEstudio) THEN
		SELECT 'Ya existe un rol con el nombre indicado.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
		SET pUsuario = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuarioGestion);
        
        SET pIdRolEstudio = (SELECT COALESCE(MAX(IdRolEstudio),0) + 1 FROM RolesEstudio);
        INSERT INTO RolesEstudio VALUES(pIdRolEstudio, pIdEstudio, pRolEstudio);
        
        INSERT INTO aud_RolesEstudio 
        SELECT 0, NOW(), pUsuario, pIP, pUserAgent, pApp, 'ALTA', 'I', RolesEstudio.* 
        FROM RolesEstudio WHERE IdRolEstudio = pIdRolEstudio;
        
        SELECT CONCAT('OK', pIdRolEstudio) Mensaje;
	COMMIT;
END $$
DELIMITER ;
