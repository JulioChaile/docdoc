CREATE TABLE `Provincias` (
  `IdProvincia` int(11) NOT NULL,
  `Provincia` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`IdProvincia`),
  UNIQUE KEY `UI_Provincia` (`Provincia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
