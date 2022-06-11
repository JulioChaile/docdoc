DROP PROCEDURE IF EXISTS `dsp_contar_contactos_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_contar_contactos_estudio`(pCadena varchar(50), pIdEstudio int, pTipo char(1))
BEGIN
	/*
    Permite contar tipos de caso filtrándolos por una cadena de búsqueda. Ordena por TipoCaso.
    */
    SET pCadena = COALESCE(pCadena,'');
    SET pTipo = COALESCE(pTipo,'');
    
    SELECT		COUNT(*)
    FROM		ContactosEstudio
    WHERE		(Nombres LIKE CONCAT('%',pCadena,'%') OR Apellidos LIKE CONCAT('%',pCadena,'%')) AND
                (IdEstudio = pIdEstudio OR pIdEstudio = 0) AND
                (Tipo = pTipo OR pTipo = '');
END $$
DELIMITER ;
