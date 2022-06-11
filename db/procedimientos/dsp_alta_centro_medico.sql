DROP PROCEDURE IF EXISTS `dsp_alta_centro_medico`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_centro_medico`(pCentroMedico varchar(200), pTelefono varchar(45), pDireccion varchar(45))
PROC: BEGIN
	/*
    Permite crear mediadores controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + Id del mediador creado o un mensaje de error en Mensaje.
    */
	-- Control de parámetros vacíos
    IF pCentroMedico IS NULL OR pCentroMedico = '' THEN
		SELECT 'Debe indicar el nombre de el centro medico.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT 1 FROM CentrosMedicos WHERE CentroMedico = pCentroMedico) THEN
		SELECT 'El centro medico indicado ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        INSERT INTO CentrosMedicos SELECT 0, pCentroMedico, pTelefono, pDireccion;

        SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
	COMMIT;
END $$
DELIMITER ;
