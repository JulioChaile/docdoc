DROP PROCEDURE IF EXISTS `dsp_dame_usuario_por_email`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_usuario_por_email`(pEmail varchar(120))
BEGIN
	/*
    Permite instanciar un usuario por Email desde la base de datos.
    */
    -- Control de errores
    IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE Email = pEmail) THEN
        SELECT "El mail ingresado no pertenece a ningun usuario registrado" Mensaje;
    ELSE
        SELECT		*
        FROM		Usuarios
        WHERE		Email = pEmail;
    END IF;
END $$
DELIMITER ;
