DELIMITER //

CREATE FUNCTION valor_caso(
    parametro_IdCaso BIGINT
) RETURNS DECIMAL(10,2)
BEGIN
    DECLARE suma_total DECIMAL(10,2);

    SELECT SUM(
            CAST(IFNULL(JSON_VALUE(pc_aux.ValoresParametros, '$.Cuantificacion.GastosCuracion'), 0) AS DECIMAL(10,2)) +
            CAST(IFNULL(JSON_VALUE(pc_aux.ValoresParametros, '$.Cuantificacion.DañoMoral'), 0) AS DECIMAL(10,2)) +
            CAST(IFNULL(JSON_VALUE(pc_aux.ValoresParametros, '$.Vehiculo.ValorReparacion'), 0) AS DECIMAL(10,2))
        )
    INTO suma_total
    FROM PersonasCaso pc_aux
    WHERE pc_aux.IdCaso = parametro_IdCaso
        AND pc_aux.ValoresParametros IS NOT NULL
        AND pc_aux.ValoresParametros != '[]'
        AND pc_aux.Observaciones = 'Actor';

    RETURN IFNULL(suma_total, 0);
END//

DELIMITER ;