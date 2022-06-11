DROP PROCEDURE IF EXISTS `dsp_traer_persona_padron`;
DELIMITER $$
CREATE PROCEDURE `dsp_traer_persona_padron`(pDocumento varchar(100))
PROC: BEGIN
	/*
    Permite instanciar una persona del padron.
	*/
    -- Control de parametros vacios
    IF pDocumento IS NULL THEN
		SELECT 'Debe indicar un documento.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parametros incorrectos
    IF EXISTS (SELECT 1 FROM Padron2019 WHERE DNI = CONCAT('', pDocumento) LIMIT 1) THEN
		SELECT 		CONCAT(p.APELLIDO, ', ', p.Nombre) Persona, CONCAT(p.DOMICILIO, ', ', p.Localidad, ', ', p.Departamento) Domicilio, 'OK' Mensaje
        FROM		Padron2019 p
        WHERE		DNI = CONCAT('', pDocumento)
        LIMIT		1;
	ELSEIF EXISTS (SELECT 1 FROM Padron WHERE DNI = CONCAT('', pDocumento) LIMIT 1) THEN
        SELECT 		p.Persona Persona, CONCAT(p.DOMICILIO, ', ', p.LOCALIDAD, ', ', p.DEPARTAMENTO) Domicilio, 'OK' Mensaje
        FROM		Padron p
        WHERE		DNI = CONCAT('', pDocumento)
        LIMIT		1;
    ELSE
		SELECT 'El documento ingresado no se encuentra en el padron.' Mensaje;
        LEAVE PROC;
	END IF;
END $$
DELIMITER ;
