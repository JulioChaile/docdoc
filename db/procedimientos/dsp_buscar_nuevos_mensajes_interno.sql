DROP PROCEDURE IF EXISTS `dsp_buscar_nuevos_mensajes_interno`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_nuevos_mensajes_interno`(pIdCaso int, pIdUsuario int, pCliente char(1))
PROC: BEGIN
    /*
    Permite obtener todos los mensajes no leídos del usuario.
    Para saber si el usuario pertenece a un chat verifica
    si el usuario mandó algún mensaje en el chat.
    */
    DECLARE pIdEstudio int;
    DECLARE pDNI varchar(200);

    SET pIdEstudio = (SELECT IdEstudio FROM UsuariosEstudio WHERE IdUsuario = pIdUsuario);
    SET pDNI = (SELECT Usuario FROM Usuarios WHERE IdUsuario = pIdUsuario);

    SELECT  b.*
    FROM    (
                SELECT      a.*, m.URL, u.Apellidos, u.Nombres, m.Tipo, c.Caratula, o.Origen, eag.EstadoAmbitoGestion
                FROM        (
                                SELECT DISTINCT mci.*
                                FROM            MensajesChatInterno mci
                                LEFT JOIN       UsuariosCaso uc USING(IdCaso)
                                WHERE           (uc.IdEstudio = pIdEstudio OR pCliente = 'N')
                                                AND (mci.IdCaso = pIdCaso OR pIdCaso = 0 OR pIdCaso = '0' OR pIdCaso = '' OR pIdCaso IS NULL)
                                ORDER BY        mci.IdMensajeChatInterno DESC
                            ) a
                LEFT JOIN   Multimedia m USING(IdMultimedia)
                LEFT JOIN   Usuarios u USING(IdUsuario)
                LEFT JOIN   Casos c USING(IdCaso)
                 LEFT JOIN  EstadoAmbitoGestion eag USING(IdEstadoAmbitoGestion)
                LEFT JOIN   PersonasCaso pc ON pc.IdCaso = c.IdCaso
                LEFT JOIN	Personas p ON p.IdPersona = pc.IdPersona
                LEFT JOIN   Origenes o USING(IdOrigen)
                WHERE       pCliente = 'S' OR p.Documento = pDNI AND pc.Observaciones = 'Actor'
                GROUP BY    a.IdMensajeChatInterno
            ) b
    WHERE   b.FechaVisto IS NULL AND b.Cliente = pCliente;
END $$
DELIMITER ;0
