DROP PROCEDURE IF EXISTS `dsp_modificar_cia_seguro`;
DELIMITER $$
CREATE PROCEDURE `dsp_modificar_cia_seguro`(pIdCiaSeguro int, pCiaSeguro varchar(255), pTelefono varchar(45), pDireccion varchar(45))
PROC: BEGIN
	/*
    Permite modificar una campaña de difusión controlando que el nombre no se encuentre en uso ya. 
    Devuelve OK o un mensaje de error en Mensaje.
    */
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
	-- Control de parámetros vacíos
	IF pIdCiaSeguro IS NULL THEN
		SELECT 'Debe indicar una compañia.' Mensaje;
        LEAVE PROC;
	END IF;
    IF pCiaSeguro IS NULL OR pCiaSeguro = '' THEN
		SELECT 'Debe indicar el nombre de la compañia.' Mensaje;
        LEAVE PROC;
	END IF;
    -- Control de parámetros incorrectos
    IF NOT EXISTS (SELECT IdCiaSeguro FROM CiasSeguro WHERE IdCiaSeguro = pIdCiaSeguro) THEN
		SELECT 'La compañia indicada no existe en el sistema.' Mensaje;
        LEAVE PROC;
	END IF;
    IF EXISTS (SELECT IdCiaSeguro FROM CiasSeguro WHERE CiaSeguro = pCiaSeguro AND IdCiaSeguro != pIdCiaSeguro) THEN
		SELECT 'El nombre de la compañia ya se encuentra en uso.' Mensaje;
        LEAVE PROC;
	END IF;
	START TRANSACTION;
        UPDATE	CiasSeguro
        SET		CiaSeguro = pCiaSeguro,
				Telefono = pTelefono,
                Direccion = pDireccion
		WHERE	IdCiaSeguro = pIdCiaSeguro;
        
        SELECT 'OK' Mensaje;
	COMMIT;
END $$
DELIMITER ;
