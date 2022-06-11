CREATE TABLE `Consultas` (
  `IdConsulta` int(11) NOT NULL,
  `IdDifusion` int(11) DEFAULT NULL,
  `Apynom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Telefono` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Texto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `FechaAlta` datetime NOT NULL,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdConsulta`),
  KEY `RefDifusiones13` (`IdDifusion`),
  CONSTRAINT `RefDifusiones13` FOREIGN KEY (`IdDifusion`) REFERENCES `Difusiones` (`IdDifusion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
