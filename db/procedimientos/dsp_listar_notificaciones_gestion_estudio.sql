DROP PROCEDURE IF EXISTS `dsp_listar_notificaciones_gestion_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_notificaciones_gestion_estudio`()
BEGIN
    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT  u.Nombres, u.Apellidos, u.Email, JSON_ARRAYAGG(
        JSON_OBJECT(
            'Detalle', mc.Detalle,
            'FechaEsperada', mc.FechaEsperada,
            'Caratula', c.Caratula,
            'NroExpediente', c.NroExpediente,
            'Carpeta', c.Carpeta,
            'Observaciones', c.Observaciones,
            'Nominacion', n.Nominacion,
            'Juzgado', j.Juzgado,
            'Jurisdiccion', jur.Jurisdiccion,
            'Objetivo', o.Objetivo,
            'Color', mc.Color,
            'UltimaAccion', (
                SELECT ma.Accion
                FROM MovimientosAcciones ma
                INNER JOIN (
                    SELECT IdMovimientoCaso, MAX(IdMovimientoAccion) AS MaxIdMovimientoAccion
                    FROM MovimientosAcciones
                    WHERE IdMovimientoCaso = mc.IdMovimientoCaso
                    GROUP BY IdMovimientoCaso
                ) MaxIds ON ma.IdMovimientoCaso = MaxIds.IdMovimientoCaso AND ma.IdMovimientoAccion = MaxIds.MaxIdMovimientoAccion
            )
        )
    ) Movimientos
    FROM MovimientosCaso mc
    INNER JOIN Casos c ON mc.IdCaso = c.IdCaso
    INNER JOIN Juzgados j ON j.IdJuzgado = c.IdJuzgado
    INNER JOIN UsuariosCaso uc ON mc.IdCaso = uc.IdCaso
    INNER JOIN Usuarios u ON uc.IdUsuario = u.IdUsuario
    LEFT JOIN Nominaciones n ON c.IdNominacion = n.IdNominacion
    LEFT JOIN MovimientosObjetivo mo ON mo.IdMovimientoCaso = mc.IdMovimientoCaso
	LEFT JOIN Objetivos o ON mo.IdObjetivo = o.IdObjetivo
    INNER JOIN Jurisdicciones jur ON j.IdJurisdiccion = jur.IdJurisdiccion
    WHERE DATE(mc.FechaEsperada) = CURDATE() AND (mc.Color = 'negative' OR mc.IdResponsable = uc.IdUsuarioCaso)
    GROUP BY u.IdUsuario;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
END $$
DELIMITER ;