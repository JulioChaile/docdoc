DROP PROCEDURE IF EXISTS `dsp_dame_valor`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_valor`(pParametro varchar(20))
BEGIN
	SELECT COALESCE(Valor, '') Valor FROM Empresa WHERE Parametro = pParametro;
END $$
DELIMITER ;
