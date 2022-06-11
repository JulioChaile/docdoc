DROP PROCEDURE IF EXISTS `dsp_alta_comisaria`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_comisaria`(pComisaria varchar(200), pTelefono varchar(45), pDireccion varchar(45))
PROC: BEGIN
	/*
    Permite crear mediadores controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK + Id del mediador creado o un mensaje de error en Mensaje.
    */
	-- Control de parámetros vacíos
    IF pComisaria IS NULL OR pComisaria = '' THEN
		SELECT 'Debe indicar el nombre de la comisaria.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF EXISTS (SELECT 1 FROM Comisarias WHERE Comisaria = pComisaria) THEN
		SELECT 'La comisaria indicada ya existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    START TRANSACTION;
        INSERT INTO Comisarias SELECT 0, pComisaria, pTelefono, pDireccion;

        SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
	COMMIT;
END $$
DELIMITER ;
