DROP PROCEDURE IF EXISTS `dsp_alta_cia_seguro`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_cia_seguro`(pCiaSeguro varchar(200), pTelefono varchar(45), pDireccion varchar(45))
PROC: BEGIN
	/*
    Permite crear mediadores controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + Id del mediador creado o un mensaje de error en Mensaje.
    */
	-- Control de parámetros vacíos
    IF pCiaSeguro IS NULL OR pCiaSeguro = '' THEN
		SELECT 'Debe indicar el nombre de la compañia de seguro.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT 1 FROM CiasSeguro WHERE CiaSeguro = pCiaSeguro) THEN
		SELECT 'La compañia de seguro indicada ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        INSERT INTO CiasSeguro SELECT 0, pCiaSeguro, pTelefono, pDireccion;

        SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
	COMMIT;
END $$
DELIMITER ;
