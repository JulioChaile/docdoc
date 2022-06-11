DROP PROCEDURE IF EXISTS `dsp_dame_usuario_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_usuario_estudio`(pIdUsuario int)
BEGIN
	/*
    Permite instanciar un usuario de estudio desde la base de datos.
    */
    
    SELECT 		u.Nombres, u.Apellidos, u.Usuario, u.Email, u.FechaUltIntento, u.Observaciones,
				re.RolEstudio, ue.IdRolEstudio, ue.IdUsuarioPadre, ue.IdEstudioPadre, e.Estudio,
                u.Token, u.DebeCambiarPass, ue.Estado, ue.IdEstudio, u.Telefono TelefonoUsuario, ua.TokenApp
    FROM 		UsuariosEstudio ue
    INNER JOIN	RolesEstudio re ON re.IdRolEstudio = ue.IdRolEstudio
    INNER JOIN	Usuarios u ON u.IdUsuario = ue.IdUsuario
    INNER JOIN	Estudios e ON e.IdEstudio = ue.IdEstudio
    LEFT JOIN   UsuariosTokenApp ua ON ua.IdUsuario = ue.IdUsuario 
    WHERE 		ue.IdUsuario = pIdUsuario AND ue.Estado = 'A';
END $$
DELIMITER ;
