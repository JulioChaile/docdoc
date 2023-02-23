DROP PROCEDURE IF EXISTS `dsp_buscar_estadoambitogestion`;
DELIMITER $$
CREATE PROCEDURE `dsp_buscar_estadoambitogestion`(pCadena varchar(50))
BEGIN
	/*
    Permite buscar estados de ambito de gestion filtrándolos por una cadena de búsqueda.
    */
    SET pCadena = COALESCE(pCadena,'');
    
    SELECT		e.*, json_arrayagg(j.Juzgado) Juzgados
    FROM		EstadoAmbitoGestion e
    LEFT JOIN 	JuzgadosEstadosAmbitos je USING(IdEstadoAmbitoGestion)
    LEFT JOIN 	Juzgados j USING(IdJuzgado)
    WHERE		e.EstadoAmbitoGestion LIKE CONCAT('%',pCadena,'%')
    GROUP BY	e.IdEstadoAmbitoGestion
    ORDER BY    e.EstadoAmbitoGestion ASC;
END $$
DELIMITER ;
