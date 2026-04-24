-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-04-2026 a las 15:40:40
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
-- Base de datos: `universidadgerardobarrios`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aspirantes`
--

CREATE TABLE `aspirantes` (
  `id` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `DUI` varchar(10) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL,
  `telefono` varchar(9) NOT NULL,
  `telefono_alternativo` varchar(9) DEFAULT NULL,
  `carrera` varchar(100) NOT NULL,
  `turno` enum('Matutino','Vespertino','Fin de Semana') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aspirantes`
--

INSERT INTO `aspirantes` (`id`, `nombres`, `apellidos`, `DUI`, `correo_electronico`, `telefono`, `telefono_alternativo`, `carrera`, `turno`) VALUES
(1, 'Carlos Alberto', 'López Martínez', '01234567-8', 'carlos.lopez@correo.com', '2234-5678', NULL, 'Ingeniería en Sistemas', 'Matutino'),
(2, 'María José', 'García Rodríguez', '02345678-9', 'maria.garcia@correo.com', '2234-5679', '7890-1234', 'Licenciatura en Administración', 'Vespertino'),
(3, 'José Antonios', 'Hernández Cuéllar', '03456789-0', 'jose.hernandez@correo.com', '2234-5680', NULL, 'Ingeniería Civil', 'Fin de Semana'),
(5, 'Luis Fernando', 'Rodríguez Vásquez', '05678901-2', 'luis.rodriguez@correo.com', '2234-5682', NULL, 'Ingeniería Industrial', 'Vespertino'),
(6, 'diego', 'montoya', '12345678-0', 'xd@yo.com', '1234-5453', NULL, 'Licenciatura en Psicología', 'Fin de Semana');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo_usuario` enum('admin','usuario') NOT NULL DEFAULT 'usuario',
  `nombre_completo` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `tipo_usuario`, `nombre_completo`, `email`, `fecha_registro`) VALUES
(1, 'admin', 'password123', 'admin', 'Administrador del Sistema', 'admin@ugb.edu.sv', '2026-04-24 13:25:41'),
(2, 'usuario', 'password123', 'usuario', 'Usuario Visitante', 'usuario@ugb.edu.sv', '2026-04-24 13:25:41');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aspirantes`
--
ALTER TABLE `aspirantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `DUI` (`DUI`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aspirantes`
--
ALTER TABLE `aspirantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
