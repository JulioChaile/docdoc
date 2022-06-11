CREATE TABLE `Permisos` (
  `IdPermiso` int(11) NOT NULL,
  `IdPermisoPadre` int(11) DEFAULT NULL,
  `Permiso` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Observaciones` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Orden` tinyint(4) NOT NULL,
  `Procedimiento` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`IdPermiso`),
  UNIQUE KEY `UI_Permiso` (`Permiso`),
  UNIQUE KEY `UI_OrdenIdPermisoPadre` (`Orden`,`IdPermisoPadre`),
  KEY `RefPermisos6` (`IdPermisoPadre`),
  CONSTRAINT `RefPermisos6` FOREIGN KEY (`IdPermisoPadre`) REFERENCES `Permisos` (`IdPermiso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
