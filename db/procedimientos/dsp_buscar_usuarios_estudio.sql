DROP PROCEDURE IF EXISTS `dsp_buscar_usuarios_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_usuarios_estudio`(pIdEstudio int, pCadena varchar(100), pIncluyeBajas char(1))
BEGIN
	/*
    Permite buscar usuarios en un estudio filtrándolos por una cadena de búsqueda e 
    indicando si se incluyen o no los dados de baja en pIncluyeBajas = [S|N]. 
    Ordena por Apellidos, Nombres.
    */
    IF pIncluyeBajas IS NULL OR pIncluyeBajas = '' OR pIncluyeBajas NOT IN('S','N') THEN
		SET pIncluyeBajas = 'N';
	END IF;
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		u.Nombres, u.Apellidos, u.Email, u.IdRol, u.FechaAlta, 
				u.FechaUltIntento, ue.IdUsuario, u.Estado EstadoUsuario, u.Usuario, u.Telefono, re.RolEstudio,
                re.IdRolEstudio, ue.Estado, u.Observaciones, ua.TokenApp
	FROM		Usuarios u
    INNER JOIN	UsuariosEstudio ue USING (IdUsuario)
    INNER JOIN	RolesEstudio re USING (IdRolEstudio)
    LEFT JOIN   UsuariosTokenApp ua ON ua.IdUsuario = ue.IdUsuario 
    WHERE		ue.IdEstudio = pIdEstudio AND
				(
					CONCAT(u.Apellidos,', ',u.Nombres) LIKE CONCAT('%',pCadena,'%') OR 
                    u.Usuario LIKE CONCAT('%', pCadena, '%')
				) AND
                ((pIncluyeBajas = 'N' AND ue.Estado = 'A') OR pIncluyeBajas = 'S')
	ORDER BY	u.Apellidos, u.Nombres;
END $$
DELIMITER ;
