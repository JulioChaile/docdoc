DROP PROCEDURE IF EXISTS `dsp_listar_comentarios_sin_leer`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_comentarios_sin_leer`(pIdUsuario int)
PROC: BEGIN
    SELECT DISTINCT     c.IdCaso, c.Caratula
    FROM                UsuariosComentarioCaso ucc
    INNER JOIN          Casos c USING(IdCaso)
    WHERE               ucc.IdUsuario = pIdUsuario AND
                        ucc.FechaVisto IS NULL;
END $$
DELIMITER ;
