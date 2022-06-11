CREATE TABLE `Objetivos` (
  `IdObjetivo` int(11) NOT NULL,
  `IdCaso` bigint(20) NOT NULL,
  `Objetivo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `FechaAlta` datetime NOT NULL,
  PRIMARY KEY (`IdObjetivo`),
  KEY `RefCasos84` (`IdCaso`),
  CONSTRAINT `RefCasos84` FOREIGN KEY (`IdCaso`) REFERENCES `Casos` (`IdCaso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
