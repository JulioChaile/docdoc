DROP PROCEDURE IF EXISTS `dsp_listar_movimientos_sin_realizar_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_movimientos_sin_realizar_caso`(pJWT varchar(500), pIdCaso bigint)
PROC: BEGIN
	/*
    Permite listar todos los movimientos de un caso. Lista todos si el IdCaso = 0.
    Ordena por FechaRealizado
    */
    DECLARE pIdEstudio, pIdUsuario int;
    
    SET pIdUsuario = (SELECT COALESCE(IdUsuario,0) FROM Usuarios WHERE Token = pJWT AND Estado = 'A');
    IF EXISTS (SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario AND IdRol IS NULL) THEN
		IF pIdUsuario = 0 THEN
			SELECT NULL;
			LEAVE PROC;
		END IF;
		SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario AND Estado = 'A');
		IF pIdEstudio IS NULL THEN
			SELECT NULL;
			LEAVE PROC;
		END IF;
    END IF;

	SET sort_buffer_size = 256000000;

    SELECT DISTINCT	u.IdUsuario IdUsuarioResponsable, 
				CONCAT(u.Apellidos, ', ', u.Nombres) UsuarioResponsable,
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('URL', Url, 'Tipo', Tipo)) 
                FROM MultimediaMovimiento 
                INNER JOIN Multimedia USING(IdMultimedia)
                WHERE IdMovimientoCaso=a.IdMovimientoCaso) Multimedia, a.*
    FROM		(SELECT		mc.*, rm.IdRecordatorioMovimiento, c.Caratula, tm.TipoMovimiento, o.IdObjetivo, o.Objetivo, e.IdEvento, e.Comienzo ComienzoEvento, audmc.UsuarioAud UsuarioEdicion, IFNULL(om.Posicion, 'U') Posicion, JSON_ARRAYAGG(JSON_OBJECT(
														'IdMovimientoAccion', ma.IdMovimientoAccion,
														'Accion', ma.Accion,
														'FechaAccion', ma.FechaAccion,
														'IdUsuario', ma.IdUsuario,
														'Apellidos', uma.Apellidos, 'Nombres', uma.Nombres
													)) Acciones
				FROM		MovimientosCaso mc
				INNER JOIN	TiposMovimiento tm USING (IdTipoMov)
                LEFT JOIN	MovimientosObjetivo mo USING (IdMovimientoCaso)
				LEFT JOIN	Objetivos o USING (IdObjetivo)
				LEFT JOIN	EventosMovimientos em USING (IdMovimientoCaso)
				LEFT JOIN	Eventos e USING (IdEvento)
				INNER JOIN	Casos c ON c.IdCaso = mc.IdCaso
				INNER JOIN	UsuariosCaso uc ON uc.IdCaso = c.IdCaso
				LEFT JOIN	MovimientosAcciones ma ON ma.IdMovimientoCaso = mc.IdMovimientoCaso
				LEFT JOIN	Usuarios uma ON uma.IdUsuario = ma.IdUsuario
				LEFT JOIN 	OrdenMovs om ON om.IdMovimientoCaso = mc.IdMovimientoCaso
				LEFT JOIN	RecordatorioMovimiento rm ON rm.IdMovimientoCaso = mc.IdMovimientoCaso
				LEFT JOIN 	(SELECT UsuarioAud, amc.IdMovimientoCaso, amc.Id FROM aud_MovimientosCaso amc WHERE amc.Motivo = 'MODIFICAR' AND amc.TipoAud = 'D' ORDER BY Id DESC) audmc ON mc.IdMovimientoCaso = audmc.IdMovimientoCaso
				WHERE		c.Estado != 'B'
							AND (pIdCaso = 0 OR mc.IdCaso = pIdCaso)
							AND mc.FechaRealizado IS NULL GROUP BY mc.IdMovimientoCaso
							ORDER BY	COALESCE(mc.FechaEdicion, mc.FechaAlta) DESC) a
	LEFT JOIN	UsuariosCaso uc ON a.IdResponsable=uc.IdUsuarioCaso
    LEFT JOIN	Usuarios u ON u.IdUsuario = uc.IdUsuario;
END $$
DELIMITER ;
