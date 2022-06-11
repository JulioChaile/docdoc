CREATE TABLE `CentrosMedicos` (
  `IdCentroMedico` int(11) NOT NULL,
  `CentroMedico` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Telefono` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Direccion` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`IdCentroMedico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
