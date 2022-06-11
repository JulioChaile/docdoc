DROP PROCEDURE IF EXISTS `dsp_buscar_avanzado_usuarios`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_avanzado_usuarios`(pTipo char(1), pCadena varchar(100), pIncluyeBajas char(1))
PROC: BEGIN
	/*
    Permite buscar usuarios filtrándolos por tipo de búsqueda en pTipo = N: Apellidos, Nombres - U: Usuario,
    una cadena de búsqueda e indicando si en incluyen o no los dados de baja en pIncluyeBajas = [S | N]. 
    Ordena por Apellidos, Nombres.
    */
    IF pIncluyeBajas IS NULL OR pIncluyeBajas = '' OR pIncluyeBajas NOT IN('S','N') THEN
		SET pIncluyeBajas = 'N';
	END IF;
    IF pTipo IS NULL OR pTipo = '' OR pTipo NOT IN('N','U') THEN
		SET pTipo = 'U';
	END IF;
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		IdUsuario, Nombres, Apellidos, Usuario, Token, Email, IntentosPass,
				FechaUltIntento, FechaAlta, DebeCambiarPass, u.Estado, u.Observaciones, u.Telefono,
                r.Rol                
    FROM		Usuarios u
    INNER JOIN	Roles r USING (IdRol)
    WHERE		(pIncluyeBajas = 'S' OR u.Estado = 'A') AND
				(
					(pTipo = 'N' AND CONCAT(u.Apellidos,', ',u.Nombres) LIKE CONCAT('%',pCadena,'%')) OR
					(pTipo = 'U' AND u.Usuario LIKE CONCAT('%',pCadena,'%')) 
				)
	ORDER BY	u.Apellidos, u.Nombres;
END $$
DELIMITER ;
