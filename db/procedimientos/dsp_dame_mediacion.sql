DROP PROCEDURE IF EXISTS `dsp_dame_mediacion`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_mediacion`(pIdMediacion int)
BEGIN
	/*
    Permite instanciar una mediador desde la base de datos.
    */
    SELECT	    m.*, bo.Bono, bo.Color ColorBono, be.Beneficio, c.IdChatMediador, ebe.EstadoBeneficio,
                JSON_OBJECT(
                    'IdMediador', me.IdMediador,
                    'Nombre', me.Nombre,
                    'Registro', me.Registro,
                    'MP', me.MP,
                    'Domicilio', me.Domicilio,
                    'Telefono', me.Telefono,
                    'Email', me.Email
                ) Mediador
    FROM	    Mediaciones m
    LEFT JOIN   BonosMediacion bo USING(IdBono)
    LEFT JOIN   BeneficiosMediacion be USING(IdBeneficio)
    LEFT JOIN   EstadosBeneficioMediacion ebe USING(IdEstadoBeneficio)
    LEFT JOIN   Mediadores me USING(IdMediador)
    LEFT JOIN   ChatsMediadores c USING(IdMediador)
    WHERE	    IdMediacion = pIdMediacion;
END $$
DELIMITER ;
