DROP PROCEDURE IF EXISTS `dsp_listar_eventos_cliente`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_eventos_cliente`(pJWT varchar(500), pUsuario varchar(50))
PROC: BEGIN
    SELECT      mc.IdMovimientoCaso, mc.Detalle, mc.FechaEsperada, c.Caratula, e.Comienzo FechaEvento, c.IdCaso
    FROM        MovimientosClientes mcc
    LEFT JOIN   MovimientosCaso mc USING(IdMovimientoCaso)
    INNER JOIN  EventosMovimientos em USING(IdMovimientoCaso)
    INNER JOIN  Eventos e USING(IdEvento)
    INNER JOIN  Casos c ON mcc.IdCaso = c.IdCaso
    LEFT JOIN   PersonasCaso pc ON pc.IdCaso = c.IdCaso
    LEFT JOIN   Personas p USING(IdPersona)
    WHERE       p.Documento = pUsuario AND mc.FechaEsperada >= NOW();
END $$
DELIMITER ;
