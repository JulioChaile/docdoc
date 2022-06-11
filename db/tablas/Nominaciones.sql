CREATE TABLE `Nominaciones` (
  `IdNominacion` int(11) NOT NULL,
  `IdJuzgado` int(11) NOT NULL,
  `Nominacion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdNominacion`,`IdJuzgado`),
  UNIQUE KEY `UI_IdNominacion` (`IdNominacion`),
  KEY `RefJuzgados20` (`IdJuzgado`),
  CONSTRAINT `RefJuzgados20` FOREIGN KEY (`IdJuzgado`) REFERENCES `Juzgados` (`IdJuzgado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
