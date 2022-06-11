DROP PROCEDURE IF EXISTS `dsp_dame_rol_tipocaso`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_rol_tipocaso`(pIdRTC smallint)
BEGIN
	/*
	Permite instancar un rol de tipo de caso desde la base de datos.
    */
    SELECT		rtc.*, tc.TipoCaso
    FROM		RolesTipoCaso rtc
    INNER JOIN	TiposCaso tc USING (IdTipoCaso)
    WHERE		rtc.IdRTC = pIdRTC;
END $$
DELIMITER ;
