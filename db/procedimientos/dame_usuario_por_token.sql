DROP PROCEDURE IF EXISTS `dame_usuario_por_token`;
DELIMITER $$
CREATE PROCEDURE `dame_usuario_por_token`(pJWT varchar(500))
BEGIN
	/*
    Permite instanciar un usuario desde la base de datos a partir del token de sesión.
    */
    SELECT 	*
    FROM	Usuarios
    WHERE	Token = pJWT;
END $$
DELIMITER ;
