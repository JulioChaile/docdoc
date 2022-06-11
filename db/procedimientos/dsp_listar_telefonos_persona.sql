DROP PROCEDURE IF EXISTS `dsp_listar_telefonos_persona`;
DELIMITER $$
CREATE PROCEDURE `dsp_listar_telefonos_persona`(pIdPersona int)
BEGIN
	/*
    Permite listar los teléfonos de una persona.
    Ordena por FechaAlta.
    */
    SELECT 	Detalle, Telefono, FechaAlta
    FROM	Telefonos
    WHERE	IdPersona = pIdPersona;
END $$
DELIMITER ;
