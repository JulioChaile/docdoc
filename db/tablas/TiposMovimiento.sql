CREATE TABLE `TiposMovimiento` (
  `IdTipoMov` int(11) NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  `TipoMovimiento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Categoria` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`IdTipoMov`),
  KEY `UI_CategoriaTipoMovimiento` (`IdEstudio`,`Categoria`,`TipoMovimiento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
