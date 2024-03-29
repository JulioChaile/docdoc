CREATE TABLE `Juzgados` (
  `IdJuzgado` int NOT NULL,
  `IdJurisdiccion` int NOT NULL,
  `Juzgado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ModoGestion` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Color` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT '000000',
  PRIMARY KEY (`IdJuzgado`,`IdJurisdiccion`),
  UNIQUE KEY `UI_Juzgado` (`Juzgado`),
  UNIQUE KEY `UI_IdJuzgado` (`IdJuzgado`),
  KEY `RefJurisdicciones19` (`IdJurisdiccion`),
  CONSTRAINT `RefJurisdicciones19` FOREIGN KEY (`IdJurisdiccion`) REFERENCES `Jurisdicciones` (`IdJurisdiccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;