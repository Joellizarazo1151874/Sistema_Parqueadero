-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-04-2025 a las 21:34:39
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ginuss_smartpark`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nombre`, `telefono`, `correo`, `fecha_registro`) VALUES
(1, 'Juan Pérez', '3001234567', 'juanperez@mail.com', '2025-03-10 18:32:30'),
(2, 'Ana Gómez', '3107654321', 'anagomez@mail.com', '2025-03-10 18:32:30'),
(3, 'Joel Lizarazo', '3209939817', 'correo@ejemplo.com', '2025-03-26 22:15:38'),
(5, 'Marcos Mejia', '3209939816', 'correo2@ejemplo.com', '2025-03-26 22:17:15'),
(6, 'Anderson Patiño', '3209939812', 'correo3@ejemplo.com', '2025-03-26 22:25:14'),
(7, 'Angelica castillo', '3121525456', 'correo4@ejemplo.com', '2025-03-26 22:30:58'),
(8, 'Pancho Villa', '1231231234', 'pancho@gmail.com', '2025-03-26 22:38:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidentes`
--

CREATE TABLE `incidentes` (
  `id_incidente` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_registro` int(11) DEFAULT NULL,
  `tipo` enum('robo','daño','mal uso de espacios','PQR') NOT NULL,
  `descripcion` text NOT NULL,
  `evidencia` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_registro` int(11) DEFAULT NULL,
  `id_suscripcion` int(11) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo` enum('efectivo','transferencia') NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros_parqueo`
--

CREATE TABLE `registros_parqueo` (
  `id_registro` int(11) NOT NULL,
  `id_vehiculo` int(11) NOT NULL,
  `hora_ingreso` datetime NOT NULL,
  `hora_salida` datetime DEFAULT NULL,
  `estado` varchar(50) NOT NULL,
  `total_pagado` int(10) DEFAULT 0,
  `metodo_pago` varchar(100) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `cerrado_por` varchar(100) NOT NULL,
  `abierto_por` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registros_parqueo`
--

INSERT INTO `registros_parqueo` (`id_registro`, `id_vehiculo`, `hora_ingreso`, `hora_salida`, `estado`, `total_pagado`, `metodo_pago`, `descripcion`, `cerrado_por`, `abierto_por`) VALUES
(72, 19, '2025-03-22 11:00:46', '2025-03-25 15:08:26', 'cerrado', 151700, 'efectivo', 'Ticket #YEV52G • MOTO • Inicio: 22/3, 11:00 a. m. • Permanencia: 76h 7m', 'Andres Tellez', 'Andres Tellez'),
(73, 18, '2025-03-22 11:03:52', '2025-03-25 13:12:10', 'cerrado', 147700, 'tarjeta_debito', 'Ticket #SQL625 • AUTO • Inicio: 22/3, 11:03 a. m. • Permanencia: 74h 8m', 'Andres Tellez', 'Andres Tellez'),
(74, 20, '2025-03-25 13:06:43', '2025-03-25 13:10:20', 'cerrado', 0, 'efectivo', 'Ticket #HDN121 • MOTOCARRO • Inicio: 25/3, 01:06 p. m. • Permanencia: 0h 3m', 'Andres Tellez', 'Andres Tellez'),
(75, 22, '2025-03-25 13:11:56', '2025-03-25 15:08:04', 'cerrado', 3300, 'efectivo', 'Ticket #JSB287 • MOTO • Inicio: 25/3, 01:11 p. m. • Permanencia: 1h 56m', 'Andres Tellez', 'Andres Tellez'),
(76, 18, '2025-03-25 13:14:59', '2025-03-25 13:17:28', 'cerrado', 67, 'efectivo', 'Ticket #SQL625 • AUTO • Inicio: 21/3, 05:08 p. m. • Permanencia: 0h 0m', 'Andres Tellez', 'Andres Tellez'),
(77, 18, '2025-03-25 13:23:21', '2025-03-25 13:58:26', 'cerrado', 600, 'tarjeta_debito', 'Ticket #SQL625 • AUTO • Inicio: 25/3, 01:23 p. m. • Permanencia: 0h 35m', 'Andres Tellez', 'Andres Tellez'),
(78, 20, '2025-03-25 13:28:22', '2025-03-25 13:28:31', 'cerrado', 0, 'mercadopago', 'Ticket #HDN121 • MOTOCARRO • Inicio: 25/3, 01:28 p. m. • Permanencia: 0h 0m', 'Andres Tellez', 'Andres Tellez'),
(79, 23, '2025-03-25 13:28:54', '2025-03-25 13:39:04', 'cerrado', 300, 'mercadopago', 'Ticket #HDN123 · MOTO · Inicio: 25/3, 13:28 · Permanencia: 0h 9m', 'Andres Tellez', 'Andres Tellez'),
(80, 18, '2025-03-25 15:07:07', '2025-03-25 15:44:35', 'cerrado', 700, 'efectivo', 'Ticket #SQL625 • AUTO • Inicio: 25/3, 03:07 p. m. • Permanencia: 0h 37m', 'Andres Tellez', 'Andres Tellez'),
(81, 23, '2025-03-25 15:07:14', '2025-03-25 16:42:42', 'cerrado', 2600, 'efectivo', 'Ticket #TKN983 • CAMIONETA • Inicio: 25/3, 03:07 p. m. • Permanencia: 1h 35m', 'Andres Tellez', 'Andres Tellez'),
(82, 24, '2025-03-25 15:07:49', '2025-03-25 15:18:25', 'cerrado', 0, 'efectivo', 'Ticket #MGO18G · MOTO · Inicio: 25/3, 15:07 · Permanencia: 0h 0m (con 15 min de tolerancia)', 'Andres Tellez', 'Andres Tellez'),
(83, 25, '2025-03-25 15:07:56', '2025-03-25 15:17:55', 'cerrado', 0, 'efectivo', 'Ticket #NQS772 · AUTO · Inicio: 25/3, 15:07 · Permanencia: 0h 0m (con 15 min de tolerancia)', 'Andres Tellez', 'Andres Tellez'),
(85, 20, '2025-03-25 15:51:34', '2025-03-25 17:42:06', 'cerrado', 3100, 'efectivo', '#HDN122 ce cerro con exito', 'Andres Tellez', 'Andres Tellez'),
(86, 23, '2025-03-25 15:51:41', '2025-03-25 15:51:57', 'cerrado', 0, 'efectivo', 'Ticket #HDN123 • MOTO • Inicio: 25/3, 03:51 p. m. • Permanencia: 0h 0m', 'Andres Tellez', 'Andres Tellez'),
(87, 19, '2025-03-25 15:52:02', '2025-03-25 15:52:37', 'cerrado', 0, 'efectivo', 'Ticket #YEV52G • MOTO • Inicio: 25/3, 03:52 p. m. • Permanencia: 0h 0m', 'Andres Tellez', 'Andres Tellez'),
(88, 20, '2025-03-25 15:52:13', '2025-03-25 15:52:18', 'cerrado', 0, 'efectivo', 'Ticket #HDN121 • MOTOCARRO • Inicio: 25/3, 03:52 p. m. • Permanencia: 0h 0m', 'Andres Tellez', 'Andres Tellez'),
(90, 18, '2025-03-25 17:30:55', '2025-03-25 17:31:00', 'cerrado', 0, 'efectivo', 'Ticket #SQL625 • AUTO • Inicio: 25/3, 05:30 p. m. • Permanencia: 0h 0m', 'Andres Tellez', 'Andres Tellez'),
(93, 18, '2025-03-26 09:01:39', NULL, 'activo', 0, NULL, '', '', 'Andres Tellez'),
(94, 19, '2025-03-26 09:01:43', '2025-03-26 12:33:49', 'cerrado', 6500, 'efectivo', '#YEV52G', 'Admin Principal', 'Andres Tellez'),
(95, 26, '2025-03-26 11:48:26', '2025-03-26 11:48:40', 'cerrado', 0, 'tarjeta_debito', '#HDN121', 'Admin Principal', 'Admin Principal'),
(96, 27, '2025-03-26 11:51:53', '2025-03-26 17:52:08', 'cancelado', 0, 'cancelado', 'se fue sin pagar :C', 'Admin Principal', 'Admin Principal'),
(97, 22, '2025-03-26 11:53:06', NULL, 'activo', 0, NULL, '', '', 'Admin Principal'),
(98, 27, '2025-03-26 11:53:11', '2025-03-26 11:53:58', 'cerrado', 0, 'tarjeta_credito', '#HDN123 me cae bien', 'Admin Principal', 'Admin Principal'),
(99, 26, '2025-03-26 11:53:19', '2025-03-26 17:53:29', 'cancelado', 0, 'cancelado', 'no le crobre', 'Admin Principal', 'Admin Principal'),
(100, 19, '2025-03-26 13:07:27', NULL, 'activo', 0, NULL, '', '', 'Admin Principal'),
(101, 26, '2025-03-26 17:26:52', NULL, 'activo', 0, NULL, '', '', 'Admin Principal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes_caja`
--

CREATE TABLE `reportes_caja` (
  `id_reporte` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total_ingresos` decimal(10,2) NOT NULL,
  `efectivo` decimal(10,2) NOT NULL,
  `transferencia` decimal(10,2) NOT NULL,
  `fecha_generacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscripciones`
--

CREATE TABLE `suscripciones` (
  `id_suscripcion` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vehiculo` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL,
  `estado` enum('activa','expirada') DEFAULT 'activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifas`
--

CREATE TABLE `tarifas` (
  `id_tarifa` int(11) NOT NULL,
  `tipo_vehiculo` varchar(50) NOT NULL,
  `precio_por_hora` int(10) DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarifas`
--

INSERT INTO `tarifas` (`id_tarifa`, `tipo_vehiculo`, `precio_por_hora`, `fecha_actualizacion`) VALUES
(1, 'auto', 5, '2025-03-26 16:17:55'),
(2, 'moto', 2, '2025-03-10 18:32:30'),
(3, 'motocarro', 4, '2025-03-10 18:32:30'),
(4, 'camioneta', NULL, '2025-03-26 16:18:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `rol` varchar(50) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `contraseña`, `rol`, `fecha_registro`) VALUES
(1, 'Admin Principal', 'admin@smartpark.com', 'F865B53623B121FD34EE5426C792E5C33AF8C227', 'administrador', '2025-03-10 18:32:30'),
(2, 'Andres Tellez', 'patrocinador@smartpark.com', '85EAF14397D4A2CD0CFAE1F3395C04A31BAFB3F8', 'patrocinador', '2025-03-10 18:32:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

CREATE TABLE `vehiculos` (
  `id_vehiculo` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `placa` varchar(10) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id_vehiculo`, `id_cliente`, `placa`, `tipo`, `descripcion`) VALUES
(18, NULL, 'SQL625', 'auto', 'BLANCO con negro'),
(19, 5, 'YEV52G', 'moto', 'Honda Bonita'),
(20, NULL, 'HDN122', 'camioneta', 'MORADA'),
(21, NULL, 'SQL-623', 'auto', ''),
(22, NULL, 'JSB287', 'moto', ''),
(23, NULL, 'TKN983', 'camioneta', ''),
(24, NULL, 'MGO18G', 'moto', ''),
(25, NULL, 'NQS772', 'auto', ''),
(26, NULL, 'HDN121', 'motocarro', 'blanco'),
(27, NULL, 'HDN123', 'moto', ''),
(28, 6, 'PNG123', 'auto', 'CARRO AMARILLO'),
(29, 7, 'MIA123', 'motocarro', 'MIAABURRIDA'),
(30, 8, 'FER123', 'auto', 'un ferrari amarillo'),
(31, 8, 'VEA123', 'moto', 'BMW 1200 cilindrage');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `incidentes`
--
ALTER TABLE `incidentes`
  ADD PRIMARY KEY (`id_incidente`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_registro` (`id_registro`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_registro` (`id_registro`),
  ADD KEY `id_suscripcion` (`id_suscripcion`);

--
-- Indices de la tabla `registros_parqueo`
--
ALTER TABLE `registros_parqueo`
  ADD PRIMARY KEY (`id_registro`),
  ADD KEY `id_vehiculo` (`id_vehiculo`);

--
-- Indices de la tabla `reportes_caja`
--
ALTER TABLE `reportes_caja`
  ADD PRIMARY KEY (`id_reporte`);

--
-- Indices de la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  ADD PRIMARY KEY (`id_suscripcion`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vehiculo` (`id_vehiculo`);

--
-- Indices de la tabla `tarifas`
--
ALTER TABLE `tarifas`
  ADD PRIMARY KEY (`id_tarifa`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD PRIMARY KEY (`id_vehiculo`),
  ADD UNIQUE KEY `placa` (`placa`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `incidentes`
--
ALTER TABLE `incidentes`
  MODIFY `id_incidente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registros_parqueo`
--
ALTER TABLE `registros_parqueo`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT de la tabla `reportes_caja`
--
ALTER TABLE `reportes_caja`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  MODIFY `id_suscripcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tarifas`
--
ALTER TABLE `tarifas`
  MODIFY `id_tarifa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id_vehiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `incidentes`
--
ALTER TABLE `incidentes`
  ADD CONSTRAINT `incidentes_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `incidentes_ibfk_2` FOREIGN KEY (`id_registro`) REFERENCES `registros_parqueo` (`id_registro`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_registro`) REFERENCES `registros_parqueo` (`id_registro`),
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_suscripcion`) REFERENCES `suscripciones` (`id_suscripcion`);

--
-- Filtros para la tabla `registros_parqueo`
--
ALTER TABLE `registros_parqueo`
  ADD CONSTRAINT `registros_parqueo_ibfk_1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`);

--
-- Filtros para la tabla `suscripciones`
--
ALTER TABLE `suscripciones`
  ADD CONSTRAINT `suscripciones_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  ADD CONSTRAINT `suscripciones_ibfk_2` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`);

--
-- Filtros para la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  ADD CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
