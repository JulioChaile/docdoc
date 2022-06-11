DROP PROCEDURE IF EXISTS `dsp_dame_contacto_estudio`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_contacto_estudio`(pIdContacto int)
BEGIN
	/*
    Permite instanciar una mediador desde la base de datos.
    */
    SELECT	    *
    FROM	    ContactosEstudio
    WHERE	    IdContacto = pIdContacto;
END $$
DELIMITER ;
