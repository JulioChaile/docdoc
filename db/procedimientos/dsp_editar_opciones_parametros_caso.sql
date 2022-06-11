DROP PROCEDURE IF EXISTS `dsp_editar_opciones_parametros_caso`;
DELIMITER $$
CREATE PROCEDURE `dsp_editar_opciones_parametros_caso`(pIdOpcionesParametrosCaso int, pOpciones json)
PROC:
BEGIN
    UPDATE	OpcionesParametrosCaso
    SET		Opciones = pOpciones
    WHERE	IdOpcionesParametrosCaso = pIdOpcionesParametrosCaso;

    SELECT 'OK' Mensaje;
END $$
DELIMITER ;
