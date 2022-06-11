DROP PROCEDURE IF EXISTS `dsp_dame_password_hash`;
DELIMITER $$
CREATE PROCEDURE `dsp_dame_password_hash`(pUsuario varchar(120))
BEGIN
	/*
    Permite obtener el password hash de un usuario a partir de su documento.
    */
	IF EXISTS (SELECT Usuario FROM Usuarios WHERE Usuario = pUsuario) THEN
		SELECT	Password 
        FROM	Usuarios
        WHERE	Usuario = pUsuario;
	ELSE
		SELECT NULL Password;
	END IF;
END $$
DELIMITER ;
