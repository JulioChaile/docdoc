DROP PROCEDURE IF EXISTS `dsp_listar_roles_tipocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_roles_tipocaso`(pIdTipoCaso smallint)
BEGIN
	/*
    Permite listar los roles de un tipo de caso. Ordena por Rol.
    */
    SELECT		rtc.*
    FROM		RolesTipoCaso rtc
    INNER JOIN	TiposCaso tc USING (IdTipoCaso)
    WHERE		rtc.IdTipoCaso = pIdTipoCaso
	ORDER BY	rtc.Rol;
END $$
DELIMITER ;
