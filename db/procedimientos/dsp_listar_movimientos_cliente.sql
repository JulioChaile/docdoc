DROP PROCEDURE IF EXISTS `dsp_listar_movimientos_cliente`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_movimientos_cliente`(pJWT varchar(500), pIdCaso bigint)
PROC: BEGIN
    SELECT      mc.IdMovimientoCaso, mc.Detalle, mc.FechaAlta
    FROM        MovimientosClientes mcc
    LEFT JOIN   MovimientosCaso mc USING(IdMovimientoCaso)
    WHERE       mc.IdCaso = pIdCaso;
END $$
DELIMITER ;
