DROP PROCEDURE IF EXISTS `dsp_listar_tipocasojuzgados`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_tipocasojuzgados` (pIdTipoCaso smallint)
BEGIN
	/*
    Permite instanciar una competencia desde la base de datos.
    Retorna un array de Juzgados y Competencias en la columna 'Juzgados'.
    El array tiene el siguiente formato:
    [
        {
            "IdJuzgado": 1,
            "Juzgado": "Civil y Comercial",
            "EstadoJuzgado": "A",
            "ModoGestion": "J",
            "IdCompetencia": 3,
            "Competencia": "Civil y Comercial",
            "EstadoCompetencia": "A",
        }
    ]
    */
    SELECT	    tc.*, COALESCE(JSON_ARRAYAGG(
                    COALESCE(JSON_OBJECT(
                        'IdJuzgado', j.IdJuzgado,
                        'Juzgado', j.Juzgado,
                        'EstadoJuzgado', j.Estado,
                        'ModoGestion', j.ModoGestion,
                        'IdCompetencia', c.IdCompetencia,
                        'Competencia', c.Competencia,
                        'EstadoCompetencia', c.Estado
                ), JSON_OBJECT())), JSON_ARRAY()) Juzgados
    FROM	    TiposCaso tc
    LEFT JOIN   TiposCasosJuzgados tcj USING(IdTipoCaso)
    LEFT JOIN   Juzgados j USING(IdJuzgado)
    LEFT JOIN   Competencias c ON tcj.IdCompetencia = c.IdCompetencia
    WHERE	    tc.IdTipoCaso = pIdTipoCaso;
END $$
DELIMITER ;