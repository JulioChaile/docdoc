DELIMITER //

CREATE FUNCTION formula_vm (pMontoMensual BIGINT, pFechaNacimiento DATE, pFechaHecho DATE, incapacidad DECIMAL(10, 2))
RETURNS DECIMAL(10, 2)
BEGIN
    DECLARE edad_persona INT;
    DECLARE i DECIMAL(10, 2);
    DECLARE a DECIMAL(10, 2);
    DECLARE n INT;
    DECLARE Vn DECIMAL(10, 2);

    IF pMontoMensual IS NULL OR incapacidad IS NULL OR pFechaHecho IS NULL OR pFechaNacimiento IS NULL THEN
        RETURN NULL;
    END IF;

    SET edad_persona = ABS(TIMESTAMPDIFF(YEAR, pFechaHecho, pFechaNacimiento));

    IF edad_persona IS NULL THEN
        RETURN NULL;
    END IF;

    IF edad_persona > 55 THEN
        SET i = 0.04;
        SET a = pMontoMensual * 13 * (incapacidad / 100) * (60 / edad_persona);
        SET n = 75 - edad_persona;
    ELSE
        SET i = 0.06;
        SET a = pMontoMensual * 13 * (incapacidad / 100);
        SET n = 65 - edad_persona;
    END IF;

    SET Vn = 1 / POW(1 + i, n);

    RETURN ROUND((a * (1 - Vn) * (1 / i)), 2);
END //

DELIMITER ;