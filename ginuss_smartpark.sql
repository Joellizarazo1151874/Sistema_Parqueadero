-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-04-2025 a las 21:31:21
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
(3, 'Joel Lizarazo ', '3209939812', 'correo@ejemplo.com', '2025-03-26 22:15:38'),
(5, 'Marcos Mejia G', '3209939816', 'correo2@ejemplo.com', '2025-03-26 22:17:15'),
(8, 'Pancho Villa', '123123121', 'pancho@gmail.com', '2025-03-26 22:38:39');

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
  `abierto_por` varchar(100) NOT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'hora',
  `tiempo_horas` float DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registros_parqueo`
--

INSERT INTO `registros_parqueo` (`id_registro`, `id_vehiculo`, `hora_ingreso`, `hora_salida`, `estado`, `total_pagado`, `metodo_pago`, `descripcion`, `cerrado_por`, `abierto_por`, `tipo`, `tiempo_horas`) VALUES
(72, 19, '2025-03-22 11:00:46', '2025-03-25 15:08:26', 'cerrado', 151700, 'efectivo', 'Ticket #YEV52G • MOTO • Inicio: 22/3, 11:00 a. m. • Permanencia: 76h 7m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(73, 18, '2025-03-22 11:03:52', '2025-03-25 13:12:10', 'cerrado', 147700, 'tarjeta_debito', 'Ticket #SQL625 • AUTO • Inicio: 22/3, 11:03 a. m. • Permanencia: 74h 8m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(74, 20, '2025-03-25 13:06:43', '2025-03-25 13:10:20', 'cerrado', 0, 'efectivo', 'Ticket #HDN121 • MOTOCARRO • Inicio: 25/3, 01:06 p. m. • Permanencia: 0h 3m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(75, 22, '2025-03-25 13:11:56', '2025-03-25 15:08:04', 'cerrado', 3300, 'efectivo', 'Ticket #JSB287 • MOTO • Inicio: 25/3, 01:11 p. m. • Permanencia: 1h 56m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(76, 18, '2025-03-25 13:14:59', '2025-03-25 13:17:28', 'cerrado', 67, 'efectivo', 'Ticket #SQL625 • AUTO • Inicio: 21/3, 05:08 p. m. • Permanencia: 0h 0m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(77, 18, '2025-03-25 13:23:21', '2025-03-25 13:58:26', 'cerrado', 600, 'tarjeta_debito', 'Ticket #SQL625 • AUTO • Inicio: 25/3, 01:23 p. m. • Permanencia: 0h 35m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(78, 20, '2025-03-25 13:28:22', '2025-03-25 13:28:31', 'cerrado', 0, 'mercadopago', 'Ticket #HDN121 • MOTOCARRO • Inicio: 25/3, 01:28 p. m. • Permanencia: 0h 0m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(79, 23, '2025-03-25 13:28:54', '2025-03-25 13:39:04', 'cerrado', 300, 'mercadopago', 'Ticket #HDN123 · MOTO · Inicio: 25/3, 13:28 · Permanencia: 0h 9m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(80, 18, '2025-03-25 15:07:07', '2025-03-25 15:44:35', 'cerrado', 700, 'efectivo', 'Ticket #SQL625 • AUTO • Inicio: 25/3, 03:07 p. m. • Permanencia: 0h 37m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(81, 23, '2025-03-25 15:07:14', '2025-03-25 16:42:42', 'cerrado', 2600, 'efectivo', 'Ticket #TKN983 • CAMIONETA • Inicio: 25/3, 03:07 p. m. • Permanencia: 1h 35m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(82, 24, '2025-03-25 15:07:49', '2025-03-25 15:18:25', 'cerrado', 0, 'efectivo', 'Ticket #MGO18G · MOTO · Inicio: 25/3, 15:07 · Permanencia: 0h 0m (con 15 min de tolerancia)', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(83, 25, '2025-03-25 15:07:56', '2025-03-25 15:17:55', 'cerrado', 0, 'efectivo', 'Ticket #NQS772 · AUTO · Inicio: 25/3, 15:07 · Permanencia: 0h 0m (con 15 min de tolerancia)', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(85, 20, '2025-03-25 15:51:34', '2025-03-25 17:42:06', 'cerrado', 3100, 'efectivo', '#HDN122 ce cerro con exito', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(86, 23, '2025-03-25 15:51:41', '2025-03-25 15:51:57', 'cerrado', 0, 'efectivo', 'Ticket #HDN123 • MOTO • Inicio: 25/3, 03:51 p. m. • Permanencia: 0h 0m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(87, 19, '2025-03-25 15:52:02', '2025-03-25 15:52:37', 'cerrado', 0, 'efectivo', 'Ticket #YEV52G • MOTO • Inicio: 25/3, 03:52 p. m. • Permanencia: 0h 0m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(88, 20, '2025-03-25 15:52:13', '2025-03-25 15:52:18', 'cerrado', 0, 'efectivo', 'Ticket #HDN121 • MOTOCARRO • Inicio: 25/3, 03:52 p. m. • Permanencia: 0h 0m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(90, 18, '2025-03-25 17:30:55', '2025-03-25 17:31:00', 'cerrado', 0, 'efectivo', 'Ticket #SQL625 • AUTO • Inicio: 25/3, 05:30 p. m. • Permanencia: 0h 0m', 'Andres Tellez', 'Andres Tellez', 'hora', 1),
(93, 18, '2025-03-26 09:01:39', '2025-04-03 08:54:01', 'cerrado', 383200, 'efectivo', '#SQL625', 'Admin Principal', 'Andres Tellez', 'hora', 1),
(94, 19, '2025-03-26 09:01:43', '2025-03-26 12:33:49', 'cerrado', 6500, 'efectivo', '#YEV52G', 'Admin Principal', 'Andres Tellez', 'hora', 1),
(95, 26, '2025-03-26 11:48:26', '2025-03-26 11:48:40', 'cerrado', 0, 'tarjeta_debito', '#HDN121', 'Admin Principal', 'Admin Principal', 'hora', 1),
(96, 27, '2025-03-26 11:51:53', '2025-03-26 17:52:08', 'cancelado', 0, 'cancelado', 'se fue sin pagar :C', 'Admin Principal', 'Admin Principal', 'hora', 1),
(97, 22, '2025-03-26 11:53:06', '2025-04-03 08:53:59', 'cerrado', 377400, 'efectivo', '#JSB287', 'Admin Principal', 'Admin Principal', 'hora', 1),
(98, 27, '2025-03-26 11:53:11', '2025-03-26 11:53:58', 'cerrado', 0, 'tarjeta_credito', '#HDN123 me cae bien', 'Admin Principal', 'Admin Principal', 'hora', 1),
(99, 26, '2025-03-26 11:53:19', '2025-03-26 17:53:29', 'cancelado', 0, 'cancelado', 'no le crobre', 'Admin Principal', 'Admin Principal', 'hora', 1),
(100, 19, '2025-03-26 13:07:27', '2025-04-03 08:53:58', 'cerrado', 375000, 'efectivo', '#YEV52G', 'Admin Principal', 'Admin Principal', 'hora', 1),
(101, 26, '2025-03-26 17:26:52', '2025-04-03 08:53:56', 'cerrado', 366300, 'efectivo', '#HDN121', 'Admin Principal', 'Admin Principal', 'hora', 1),
(102, 34, '2025-04-02 13:14:18', '2025-04-03 08:53:54', 'cerrado', 38700, 'efectivo', '#YEV52N', 'Admin Principal', 'Admin Principal', 'hora', 1),
(103, 35, '2025-04-02 13:15:22', '2025-04-02 13:16:40', 'cerrado', 0, 'efectivo', '#YEV52R', 'Admin Principal', 'Admin Principal', 'hora', 1),
(104, 35, '2025-04-02 13:16:49', '2025-04-03 08:53:51', 'cerrado', 38700, 'efectivo', '#YEV52R', 'Admin Principal', 'Admin Principal', 'hora', 1),
(105, 36, '2025-04-02 15:38:15', '2025-04-03 08:53:50', 'cerrado', 33900, 'efectivo', '#SQL621', 'Admin Principal', 'Admin Principal', 'hora', 1),
(106, 37, '2025-04-02 17:45:18', '2025-04-03 08:53:46', 'cerrado', 29700, 'efectivo', '#SQL741', 'Admin Principal', 'Admin Principal', 'hora', 1),
(107, 18, '2025-04-03 09:01:12', '2025-04-03 09:28:33', 'cerrado', 3900, 'efectivo', '#SQL625', 'Admin Principal', 'Admin Principal', 'hora', 1),
(108, 19, '2025-04-03 09:03:40', '2025-04-03 09:28:30', 'cerrado', 200, 'efectivo', '#YEV52G', 'Admin Principal', 'Admin Principal', 'hora', 1),
(109, 38, '2025-04-03 09:22:24', '2025-04-03 09:28:31', 'cerrado', 0, 'efectivo', '#HDN123', 'Admin Principal', 'Admin Principal', 'hora', 1),
(110, 18, '2025-04-03 09:29:44', NULL, 'activo', 0, NULL, '', '', 'Admin Principal', 'hora', 1),
(111, 19, '2025-04-03 09:31:27', '2025-04-03 17:28:03', 'cancelado', 0, 'cancelado', '', 'Admin Principal', 'Admin Principal', 'hora', 1),
(112, 26, '2025-04-03 10:10:48', '2025-04-03 10:24:35', 'cerrado', 0, 'efectivo', '#SQL625', 'Admin Principal', 'Admin Principal', 'hora', 1),
(113, 21, '2025-04-03 10:18:18', '2025-04-03 10:20:55', 'cerrado', 0, 'efectivo', '#SQL-623', 'Admin Principal', 'Admin Principal', 'hora', 1),
(114, 21, '2025-04-03 10:21:02', '2025-04-03 10:27:26', 'cerrado', 1900, 'efectivo', '#HDN198', 'Admin Principal', 'Admin Principal', 'hora', 1),
(115, 39, '2025-04-03 10:27:02', NULL, 'activo', 0, NULL, '', '', 'Admin Principal', 'hora', 1),
(116, 21, '2025-04-03 10:41:30', '2025-04-03 10:44:24', 'cerrado', 1000000, 'efectivo', '#SQL-623', 'Admin Principal', 'Admin Principal', 'dia', 1),
(117, 38, '2025-04-03 10:47:00', '2025-04-03 12:58:16', 'cerrado', 1800, 'efectivo', '#HDN123', 'Admin Principal', 'Admin Principal', 'dia', 1),
(118, 21, '2025-04-03 10:50:49', '2025-04-03 10:59:16', 'cerrado', 0, 'efectivo', '#SQL-623', 'Admin Principal', 'Admin Principal', 'mes', 1),
(119, 21, '2025-04-03 10:59:46', '2025-04-03 11:20:18', 'cerrado', 500, 'efectivo', '#HDN123', 'Admin Principal', 'Admin Principal', 'año', 1),
(120, 21, '2025-04-03 11:41:54', '2025-04-03 19:25:55', 'cancelado', 0, 'cancelado', '', 'Admin Principal', 'Admin Principal', '4 Horas', 4),
(121, 40, '2025-04-03 11:47:39', '2025-04-03 12:58:05', 'cerrado', 10400, 'efectivo', '#HDN123', 'Admin Principal', 'Admin Principal', 'semana', 168),
(122, 21, '2025-04-03 13:01:36', '2025-04-03 13:29:20', 'cerrado', 90000, 'efectivo', '#SQL-623', 'Admin Principal', 'Admin Principal', '4_horas', 1),
(123, 38, '2025-04-03 13:01:56', '2025-04-03 13:28:30', 'cerrado', 10800, 'efectivo', '#HDN123', 'Admin Principal', 'Admin Principal', '8_horas', 1),
(124, 19, '2025-04-03 13:03:32', '2025-04-03 13:28:28', 'cerrado', 1600, 'efectivo', '#SQL-623', 'Admin Principal', 'Admin Principal', 'dia', 1);

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
  `hora` int(10) DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `dia` int(11) DEFAULT 0,
  `mes` int(11) DEFAULT 0,
  `año` int(11) DEFAULT 0,
  `semana` int(11) DEFAULT 0,
  `4_horas` int(11) DEFAULT 0,
  `8_horas` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarifas`
--

INSERT INTO `tarifas` (`id_tarifa`, `tipo_vehiculo`, `hora`, `fecha_actualizacion`, `dia`, `mes`, `año`, `semana`, `4_horas`, `8_horas`) VALUES
(1, 'auto', 5000, '2025-04-03 18:30:41', 20000, 300000, 1500000, 1500000, 200000, 200000),
(2, 'moto', 2000, '2025-04-03 18:30:41', 100000, 0, 200000, 0, 0, 0),
(4, 'camioneta', 2500, '2025-04-03 18:30:41', 150000, 300000, 200000, 0, 0, 0),
(7, 'motocarro', 2000, '2025-04-03 18:30:41', 50000, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tolerancia`
--

CREATE TABLE `tolerancia` (
  `tipo` varchar(50) NOT NULL,
  `tolerancia` int(11) DEFAULT NULL,
  `tiempo` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tolerancia`
--

INSERT INTO `tolerancia` (`tipo`, `tolerancia`, `tiempo`) VALUES
('hora', 0, 1),
('dia', 0, 24);

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
(4, 'Andres Tellez', 'operador@smartpark.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', 'operador', '2025-04-04 02:26:12');

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
(18, NULL, 'SQL625', 'auto', 'negro con blanco :\"3'),
(19, 5, 'YEV52G', 'moto', 'Honda Bonita'),
(20, NULL, 'HDN122', 'camioneta', 'MORADA'),
(21, NULL, 'SQL-623', 'auto', ''),
(22, NULL, 'JSB287', 'moto', ''),
(23, NULL, 'TKN983', 'camioneta', ''),
(24, NULL, 'MGO18G', 'moto', ''),
(25, NULL, 'NQS772', 'auto', ''),
(26, NULL, 'HDN121', 'motocarro', 'blanco'),
(27, NULL, 'HDN432', 'moto', ''),
(28, NULL, 'PNG123', 'auto', 'CARRO AMARILLO'),
(30, 8, 'FER432', 'auto', 'un ferrari amarillo'),
(31, 8, 'VEA121', 'moto', 'BMW 1200 cilindro'),
(32, NULL, 'PER122', 'auto', 'AUTO BACANO'),
(33, NULL, 'MAS989', 'moto', ''),
(34, NULL, 'YEV52N', 'auto', ''),
(35, NULL, 'YEV52R', 'auto', ''),
(36, NULL, 'SQL621', 'motocarro', ''),
(37, NULL, 'SQL741', 'auto', ''),
(38, NULL, 'HDN123', 'auto', ''),
(39, NULL, 'HCN196', 'camioneta', ''),
(40, NULL, 'SQL698', 'auto', '');

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
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

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
  MODIFY `id_tarifa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `vehiculos`
--
ALTER TABLE `vehiculos`
  MODIFY `id_vehiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

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
