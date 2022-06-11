DROP PROCEDURE IF EXISTS `dsp_dame_usuario_por_telefono`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_usuario_por_telefono`(pTelefono varchar(20))
BEGIN
	/*
    Permite instanciar un usuario por telefono desde la base de datos.
    */
    -- Control de errores
    IF NOT EXISTS (SELECT 1 FROM Usuarios WHERE REPLACE(REPLACE(Telefono, ' ', ''), '-', '') LIKE CONCAT('%', REPLACE(REPLACE(PTelefono, ' ', ''), '-', ''), '%') ) THEN
        SELECT "El telefono ingresado no pertenece a ningun usuario registrado" Mensaje;
    ELSE
        SELECT		*
        FROM		Usuarios
        WHERE		REPLACE(REPLACE(Telefono, ' ', ''), '-', '') LIKE CONCAT('%', REPLACE(REPLACE(PTelefono, ' ', ''), '-', ''), '%');
    END IF;
END $$
DELIMITER ;
