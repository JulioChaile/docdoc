DROP PROCEDURE IF EXISTS `aux_crear_tablas_auditoria`;
DELIMITER $$
CREATE PROCEDURE `aux_crear_tablas_auditoria`(pSchema varchar(100))
SALIR: BEGIN
	/*
    Procedimiento que permite crear todas las tablas de auditoría de la base de datos indicada en pSchema.
    */
    DECLARE pIndice int;
    DECLARE pTabla varchar(100);
    DECLARE pStmtAtributosTabla, pStmt text;
   	DECLARE EXIT HANDLER FOR SQLEXCEPTION 
		BEGIN
			SELECT CONCAT('Error al crear tabla de auditoría: aud_',pTabla) Mensaje;
		END;
    -- Obtener nombres de todas las tablas excepto aquellas que son de auditoría
    DROP TEMPORARY TABLE IF EXISTS tmp_table_names;
    SET @rowNum = 0;
    CREATE TEMPORARY TABLE tmp_table_names ENGINE = MEMORY
		SELECT 	(@rowNum := @rowNum + 1) Fila, TABLE_NAME Tabla
        FROM 	`INFORMATION_SCHEMA`.`TABLES` 
        WHERE 	`TABLE_SCHEMA` = pSchema collate utf8_spanish_ci AND 
				TABLE_NAME NOT LIKE 'aud_%' ;
	
    SET pIndice = 1;
    WHILE (pIndice <= @rowNum) DO
		SET pTabla = (SELECT Tabla FROM tmp_table_names WHERE Fila = pIndice);
		
        DROP TEMPORARY TABLE IF EXISTS tmp_atributos_tabla;
        CREATE TEMPORARY TABLE tmp_atributos_tabla ENGINE = MEMORY
			SELECT 	COLUMN_NAME AS `Field`, COLUMN_TYPE AS `Type`, IS_NULLABLE AS `Null`,
				COLUMN_KEY AS `Key`, COLUMN_DEFAULT AS `Default`
			FROM 	`INFORMATION_SCHEMA`.`COLUMNS`
			WHERE 	`TABLE_SCHEMA` = pSchema collate utf8_spanish_ci AND 
					`TABLE_NAME` = pTabla collate utf8_spanish_ci;
            
		
		SET pStmtAtributosTabla = (	SELECT 	GROUP_CONCAT(' `', Field, '` ' , IF(Type LIKE '%char%', 
												CONCAT(Type, ' COLLATE utf8_spanish_ci'), Type), ' ', 
                                                IF(`Null` = 'NO', 'NOT NULL', 'DEFAULT NULL'))
									FROM 	tmp_atributos_tabla);        
        
        SET pStmt = CONCAT('CREATE TABLE `aud_', pTabla,'` (
			`Id` bigint(20) NOT NULL AUTO_INCREMENT,
			`FechaAud` datetime NOT NULL,
			`UsuarioAud` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
			`IP` varchar(40) COLLATE utf8_spanish_ci NOT NULL,
			`UserAgent` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
			`Aplicacion` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
			`Motivo` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
			`TipoAud` char(1) COLLATE utf8_spanish_ci NOT NULL,', pStmtAtributosTabla, ',
			PRIMARY KEY (`Id`),
			KEY `IX_FechaAud` (`FechaAud`),
			KEY `IX_Usuario` (`UsuarioAud`),
			KEY `IX_IP` (`IP`),
			KEY `IX_Aplicacion` (`Aplicacion`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;');
        
        CALL dsp_eval(pStmt);
        DROP TEMPORARY TABLE IF EXISTS tmp_atributos_tabla;
		SET pIndice = pIndice + 1;
    END WHILE;
     
    DROP TEMPORARY TABLE IF EXISTS tmp_table_names;
	SELECT 'OK';
END $$
DELIMITER ;
