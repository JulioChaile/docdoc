DROP PROCEDURE IF EXISTS `dsp_listar_comentarios_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_comentarios_caso`(pIdCaso bigint)
PROC: BEGIN
    SELECT      cc.*, u.Usuario, u.Apellidos, u.Nombres,
                JSON_ARRAYAGG(JSON_OBJECT(
                                            'IdUsuario', ucc.IdUsuario,
                                            'Usuario', ux.Usuario,
                                            'Nombres', ux.Nombres,
                                            'Apellidos', ux.Apellidos,
                                            'FechaVisto', ucc.FechaVisto
                                        )) UsuariosEtiquetados
    FROM        ComentariosCaso cc
    LEFT JOIN   UsuariosComentarioCaso ucc USING(IdComentarioCaso)
    LEFT JOIN   Usuarios u ON u.IdUsuario = cc.IdUsuario
    LEFT JOIN   Usuarios ux ON ux.IdUsuario = ucc.IdUsuario
    WHERE       cc.IdCaso = pIdCaso
    GROUP BY    IdComentarioCaso
    ORDER BY    IdComentarioCaso DESC;
END $$
DELIMITER ;
