DROP PROCEDURE IF EXISTS `dsp_dame_usuario_por_token`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_usuario_por_token`(pJWT varchar(500))
BEGIN
	/*
    Permite instanciar un usuario a partir de su token. Cambia la salida dependiendo de si es un usuario administrador o un usuario cliente.
    */
    IF EXISTS (SELECT IdUsuario FROM Usuarios WHERE Token = pJWT AND IdRol IS NOT NULL) THEN
		SELECT 		'OK' Mensaje, u.IdUsuario, u.IdRol, u.Nombres, u.Apellidos, u.Usuario,
					u.Token, u.Email, u.DebeCambiarPass, u.Estado, r.Rol
		FROM 		Usuarios u
		INNER JOIN 	Roles r ON r.IdRol = u.IdRol
		WHERE		Token = pJWT;
	ELSEIF EXISTS (SELECT IdUsuario FROM Usuarios WHERE Token = pJWT AND IdRol IS NULL) THEN
		SELECT 		'OK' Mensaje, u.IdUsuario, u.IdRol, u.Nombres, u.Apellidos, u.Usuario,
					u.Token, u.Email, u.DebeCambiarPass, ue.IdEstudio, e.Estudio, ue.Estado,
					ue.IdUsuarioPadre, ue.IdRolEstudio, re.RolEstudio
		FROM 		Usuarios u
		INNER JOIN	UsuariosEstudio ue ON ue.IdUsuario = u.IdUsuario
		INNER JOIN	RolesEstudio re ON re.IdRolEstudio = ue.IdRolEstudio
		INNER JOIN	Estudios e ON e.IdEstudio = ue.IdEstudio
		WHERE		Token = pJWT AND ue.Estado = 'A'; 
	ELSE
		SELECT 'Error al ingresar. Contáctese con el administrador.' Mensaje;
	END IF;
END $$
DELIMITER ;
