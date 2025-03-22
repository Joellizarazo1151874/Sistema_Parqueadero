-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-03-2025 a las 17:54:41
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
(2, 'Ana Gómez', '3107654321', 'anagomez@mail.com', '2025-03-10 18:32:30');

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
(57, 19, '2025-03-20 13:03:58', '2025-03-20 13:06:57', 'cerrado', 0, 'efectivo', 'Ticket #YEV52G • MOTO • Inicio: 20/3, 01:03 p. m. • Permanencia: 0h 2m', 'Joel Lizarazo', ''),
(60, 20, '2025-03-20 13:08:20', '2025-03-20 13:17:41', 'cerrado', 0, 'efectivo', 'Ticket #HDN121 • MOTOCARRO • Inicio: 20/3, 01:08 p. m. • Permanencia: 0h 9m', 'Joel Lizarazo', ''),
(62, 20, '2025-03-21 15:38:45', '2025-03-21 16:29:48', 'cerrado', 1100, 'efectivo', 'Ticket #HDN121 • MOTOCARRO • Inicio: 21/3, 03:38 p. m. • Permanencia: 0h 51m', 'Joel Lizarazo', ''),
(63, 18, '2025-03-21 15:44:00', '2025-03-21 15:44:04', 'cerrado', 0, 'efectivo', 'Ticket #SQL625 • AUTO • Inicio: 21/3, 03:44 p. m. • Permanencia: 0h 0m', 'Joel Lizarazo', ''),
(64, 23, '2025-03-21 15:58:59', '2025-03-21 17:09:35', 'cerrado', 1800, 'efectivo', 'Ticket #HDN123 • MOTO • Inicio: 21/3, 03:58 p. m. • Permanencia: 1h 10m', 'Joel Lizarazo', ''),
(66, 18, '2025-03-21 11:59:20', '2025-03-21 16:31:39', 'cerrado', 8500, 'efectivo', 'Ticket #SQL625 • AUTO • Inicio: 21/3, 11:59 a. m. • Permanencia: 4h 32m', 'Joel Lizarazo', ''),
(67, 19, '2025-03-21 16:31:47', '2025-03-21 17:07:42', 'cerrado', 600, 'mercadopago', 'Ticket #YEV52G • MOTO • Inicio: 21/3, 04:31 p. m. • Permanencia: 0h 35m', 'Patrocinador 1', ''),
(68, 18, '2025-03-21 17:08:11', '2025-03-21 17:08:14', 'cerrado', 0, 'efectivo', 'Ticket #SQL625 • AUTO • Inicio: 21/3, 05:08 p. m. • Permanencia: 0h 0m', 'Joel Lizarazo', ''),
(69, 18, '2025-03-21 17:08:47', '2025-03-21 17:09:19', 'cerrado', 0, 'efectivo', 'Ticket #SQL625 • AUTO • Inicio: 21/3, 05:08 p. m. • Permanencia: 0h 0m', 'Joel Lizarazo', ''),
(70, 18, '2025-03-21 17:09:48', '2025-03-21 17:09:50', 'cerrado', 0, 'efectivo', 'Ticket #SQL625 • AUTO • Inicio: 21/3, 05:09 p. m. • Permanencia: 0h 0m', 'Joel Lizarazo', ''),
(71, 18, '2025-03-21 17:17:37', '2025-03-22 11:01:48', 'cerrado', 34900, 'efectivo', 'Ticket #SQL625 • auto • Inicio: 21/3, 05:17 p. m. • Permanencia: 17h 44m', 'Joel Lizarazo', ''),
(72, 19, '2025-03-22 11:00:46', NULL, 'activo', 0, NULL, '', '', 'Joel Lizarazo'),
(73, 18, '2025-03-22 11:03:52', NULL, 'activo', 0, NULL, '', '', 'Joel Lizarazo');

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
  `tipo_vehiculo` enum('carro','moto','motocarro') NOT NULL,
  `precio_por_hora` decimal(10,2) NOT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarifas`
--

INSERT INTO `tarifas` (`id_tarifa`, `tipo_vehiculo`, `precio_por_hora`, `fecha_actualizacion`) VALUES
(1, 'carro', 5.00, '2025-03-10 18:32:30'),
(2, 'moto', 2.00, '2025-03-10 18:32:30'),
(3, 'motocarro', 3.50, '2025-03-10 18:32:30');

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
(2, 'Joel Lizarazo', 'patrocinador@smartpark.com', '85EAF14397D4A2CD0CFAE1F3395C04A31BAFB3F8', 'patrocinador', '2025-03-10 18:32:30');

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
(18, NULL, 'SQL625', 'auto', ''),
(19, NULL, 'YEV52G', 'moto', ''),
(20, NULL, 'HDN121', 'motocarro', ''),
(21, NULL, 'SQL-623', 'auto', ''),
(22, NULL, 'JSB287', 'moto', ''),
(23, NULL, 'HDN123', 'moto', '');

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
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

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
  MODIFY `id_tarifa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id_vehiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
