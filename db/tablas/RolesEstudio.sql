CREATE TABLE `RolesEstudio` (
  `IdRolEstudio` int(11) NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  `RolEstudio` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdRolEstudio`,`IdEstudio`),
  UNIQUE KEY `UI_IdRolEstudio` (`IdRolEstudio`),
  KEY `RefEstudios54` (`IdEstudio`),
  CONSTRAINT `RefEstudios54` FOREIGN KEY (`IdEstudio`) REFERENCES `Estudios` (`IdEstudio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
