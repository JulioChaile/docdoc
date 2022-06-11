CREATE TABLE `PermisosRol` (
  `IdPermiso` int(11) NOT NULL,
  `IdRol` tinyint(4) NOT NULL,
  PRIMARY KEY (`IdPermiso`,`IdRol`),
  KEY `RefRoles4` (`IdRol`),
  CONSTRAINT `RefPermisos5` FOREIGN KEY (`IdPermiso`) REFERENCES `Permisos` (`IdPermiso`),
  CONSTRAINT `RefRoles4` FOREIGN KEY (`IdRol`) REFERENCES `Roles` (`IdRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
