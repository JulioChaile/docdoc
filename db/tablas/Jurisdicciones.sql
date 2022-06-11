CREATE TABLE `Jurisdicciones` (
  `IdJurisdiccion` int(11) NOT NULL,
  `Jurisdiccion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdJurisdiccion`),
  UNIQUE KEY `UI_Jurisdiccion` (`Jurisdiccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
