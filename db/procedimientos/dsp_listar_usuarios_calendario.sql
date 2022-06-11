DROP PROCEDURE IF EXISTS `dsp_listar_usuarios_calendario`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_usuarios_calendario`(pIdCalendario int)
BEGIN
    SELECT	ua.*, u.Nombres, u.Apellidos, u.Email
    FROM	UsuariosACL ua
    INNER JOIN Usuarios u USING(IdUsuario)
    WHERE	ua.IdCalendario = pIdCalendario;
END $$
DELIMITER ;
