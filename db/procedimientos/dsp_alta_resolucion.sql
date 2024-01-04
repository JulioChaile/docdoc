DROP PROCEDURE IF EXISTS `dsp_alta_resolucion`;
DELIMITER $$
CREATE PROCEDURE `dsp_alta_resolucion`(pResolucion varchar(500), pFecha datetime, pMonto bigint)
PROC: BEGIN
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
            SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje;
            ROLLBACK;
		END;
    START TRANSACTION;
        INSERT INTO ResolucionesSMVM SELECT 0, pResolucion, pFecha, pMonto;

        IF pFecha >= (SELECT MAX(FechaResolucion) FROM ResolucionesSMVM) THEN
          UPDATE PersonasCaso pc
          INNER JOIN Personas p USING(IdPersona)
          INNER JOIN ParametrosCaso ppc ON ppc.IdCaso = pc.IdCaso
          SET pc.ValoresParametros = JSON_SET(
            JSON_SET(
              JSON_SET(
                JSON_SET(
                  JSON_SET(
                    pc.ValoresParametros,
                    '$.Cuantificacion.Resolucion',
                    pResolucion
                  ),
                  '$.Cuantificacion.Monto',
                  pMonto
                ),
                '$.Cuantificacion.FormulaVM',
                formula_vm(
                  pMonto,
                  p.FechaNacimiento,
                  JSON_EXTRACT(ppc.Parametros, '$.FechaHecho'),
                  JSON_EXTRACT(pc.ValoresParametros, '$.Lesiones.Incapacidad')
                )
              ),
              '$.Cuantificacion.DañoMoral',
              formula_vm(
                pMonto,
                p.FechaNacimiento,
                JSON_EXTRACT(ppc.Parametros, '$.FechaHecho'),
                JSON_EXTRACT(pc.ValoresParametros, '$.Lesiones.Incapacidad')
              ) * 0.5
            ),
            '$.Cuantificacion.GastosCuracion',
            pMonto * 0.4
          )
          WHERE pc.ValoresParametros IS NOT NULL
                AND pc.ValoresParametros != '[]'
                AND formula_vm(
                  pMonto,
                  p.FechaNacimiento,
                  JSON_EXTRACT(ppc.Parametros, '$.FechaHecho'),
                  JSON_EXTRACT(pc.ValoresParametros, '$.Lesiones.Incapacidad')
                ) IS NOT NULL;
        END IF;
        
        SELECT CONCAT('OK', LAST_INSERT_ID()) Mensaje;
	COMMIT;
END $$
DELIMITER ;
