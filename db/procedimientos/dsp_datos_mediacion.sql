DROP PROCEDURE IF EXISTS `dsp_datos_mediaciones`;
DELIMITER $$
CREATE PROCEDURE `dsp_datos_mediaciones`()
BEGIN
    SELECT (SELECT	JSON_ARRAYAGG(JSON_OBJECT(
					'IdBono', bo.IdBono,
                    'Bono', bo.Bono
				))
            FROM BonosMediacion bo) Bonos,
            (SELECT	JSON_ARRAYAGG(JSON_OBJECT(
                        'IdBeneficio', be.IdBeneficio,
                        'Beneficio', be.Beneficio
                    ))
            FROM BeneficiosMediacion be) Beneficios,
            (SELECT	JSON_ARRAYAGG(JSON_OBJECT(
                        'IdEstadoBeneficio', ebe.IdEstadoBeneficio,
                        'EstadoBeneficio', ebe.EstadoBeneficio
                    ))
            FROM EstadosBeneficioMediacion ebe) EstadosBeneficio;
END $$
DELIMITER ;
