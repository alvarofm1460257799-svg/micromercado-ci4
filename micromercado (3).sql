-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-10-2024 a las 12:50:34
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
-- Base de datos: `micromercado`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arqueo_caja`
--

CREATE TABLE `arqueo_caja` (
  `id` int(11) NOT NULL,
  `id_caja` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `monto_inicial` decimal(10,2) NOT NULL,
  `monto_final` decimal(10,2) DEFAULT NULL,
  `total_ventas` int(11) NOT NULL,
  `estatus` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

CREATE TABLE `cajas` (
  `id` int(11) NOT NULL,
  `numero_caja` varchar(10) NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `folio` int(11) NOT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_modifica` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `cajas`
--

INSERT INTO `cajas` (`id`, `numero_caja`, `nombre`, `folio`, `activo`, `fecha_alta`, `fecha_modifica`) VALUES
(1, '1', 'Caja general', 27, 1, '2024-10-12 20:24:57', '2024-10-12 20:24:57'),
(2, '2', 'Caja secundaria', 2, 1, '2024-05-16 05:25:32', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_edit` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `activo`, `fecha_alta`, `fecha_edit`) VALUES
(1, 'Bebidas', 1, '2024-06-11 04:11:01', '2024-06-11 08:11:01'),
(2, 'Snaks', 1, '2024-04-01 23:22:09', '2024-04-02 03:22:09'),
(3, 'Envasados', 1, '2024-06-11 04:10:42', '2024-06-11 08:10:42'),
(4, 'Embutidos', 1, '2024-06-11 04:11:45', '2024-06-11 08:11:45'),
(5, 'Lacteos', 1, '2024-06-11 04:10:32', '2024-06-11 08:10:32'),
(6, 'Golosinas', 1, '2024-06-11 04:11:29', '2024-06-11 08:11:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `CI` varchar(15) NOT NULL,
  `direccion` varchar(150) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_edit` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `CI`, `direccion`, `telefono`, `correo`, `activo`, `fecha_alta`, `fecha_edit`) VALUES
(1, 'Marcelo', '12345', 'Calle Suipacha', '68489986', 'marcelux@gmail.com', 1, '2024-04-23 14:02:21', '2024-04-23 13:02:21'),
(2, 'Juan', '123456', 'Av. Panamericana', '734587', 'juancito@gmail.com', 1, '2024-09-17 02:40:25', '2024-09-17 06:40:25'),
(3, 'Maycol', '1234567', 'Calle Ingavi', '6737234', 'maycol12@gmail.com', 1, '2024-06-11 08:12:54', '2024-06-11 08:12:54'),
(4, 'Alvaro', '1234568', 'Barrio los olivos', '60257799', 'alvaro123@gmail.com', 1, '2024-09-17 06:40:06', '2024-09-17 06:40:06'),
(5, 'Edwin', '123459', 'Barrio Aranjuez', '60257799', 'edwin13@gmail.com', 1, '2024-05-17 06:40:06', '2024-09-07 06:40:06'),
(6, 'Raul', '7654321', 'Barrio la Union', '60257799', 'raul89@gmail.com', 1, '2024-06-17 06:40:06', '2024-09-10 06:40:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `folio` varchar(15) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `activo` tinyint(11) NOT NULL DEFAULT 1,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `folio`, `total`, `id_usuario`, `activo`, `fecha_alta`) VALUES
(1, '66fef1f8760dd', 14.00, 1, 1, '2024-10-03 19:35:26'),
(2, '67044b5902ebc', 14.00, 1, 1, '2024-10-07 20:58:11'),
(3, '67088901a9876', 1500.00, 1, 1, '2024-10-11 02:10:21'),
(4, '6708891b89a35', 700.00, 1, 1, '2024-10-11 02:10:43'),
(5, '67088925b40db', 200.00, 1, 1, '2024-10-11 02:11:00'),
(6, '6708893dbdadd', 1100.00, 1, 1, '2024-10-11 02:11:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `valor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `nombre`, `valor`) VALUES
(1, 'tienda_nombre', 'MICROMERCADO '),
(2, 'tienda_rfc', 'XXAXX000000XXX'),
(3, 'tienda_telefono', '60257922'),
(4, 'tienda_email', 'tienda@cdp,com'),
(5, 'tienda_direccion', 'AVENIDA  SUCRE\r\n'),
(6, 'ticket_leyenda', 'Gracias por comprar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `id` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `movimiento` varchar(20) NOT NULL DEFAULT 'INGRESO',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `detalle_compra`
--

INSERT INTO `detalle_compra` (`id`, `id_compra`, `id_producto`, `nombre`, `cantidad`, `precio`, `movimiento`, `fecha_alta`) VALUES
(1, 1, 1, 'Coquito', 2, 7.00, 'INGRESO', '2024-10-03 19:35:26'),
(2, 2, 3, 'Pan', 7, 2.00, 'INGRESO', '2024-10-07 20:58:11'),
(3, 3, 2, 'Cola Cola', 100, 15.00, 'INGRESO', '2024-10-11 02:10:21'),
(4, 4, 1, 'Coquito', 100, 7.00, 'INGRESO', '2024-10-11 02:10:43'),
(5, 5, 3, 'Pan', 100, 2.00, 'INGRESO', '2024-10-11 02:11:00'),
(6, 6, 4, 'carpicola', 100, 11.00, 'INGRESO', '2024-10-11 02:11:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_roles_permisos`
--

CREATE TABLE `detalle_roles_permisos` (
  `id` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_roles_permisos`
--

INSERT INTO `detalle_roles_permisos` (`id`, `id_rol`, `id_permiso`) VALUES
(214, 2, 1),
(215, 2, 2),
(216, 2, 3),
(217, 2, 4),
(218, 2, 5),
(219, 2, 6),
(220, 2, 7),
(221, 2, 8),
(222, 2, 9),
(223, 2, 10),
(224, 2, 11),
(225, 2, 12),
(226, 2, 13),
(227, 2, 14),
(228, 2, 15),
(229, 2, 16),
(230, 2, 17),
(231, 3, 1),
(232, 3, 2),
(233, 3, 3),
(234, 3, 4),
(235, 3, 5),
(236, 3, 6),
(237, 3, 7),
(238, 3, 8),
(239, 3, 9),
(240, 3, 10),
(241, 3, 11),
(242, 3, 12),
(243, 3, 13),
(244, 3, 14),
(245, 3, 15),
(246, 3, 16),
(247, 3, 17),
(248, 4, 1),
(249, 4, 2),
(250, 4, 3),
(251, 4, 4),
(252, 4, 5),
(253, 4, 6),
(254, 4, 7),
(255, 4, 8),
(256, 4, 9),
(257, 4, 10),
(258, 4, 11),
(259, 4, 12),
(260, 4, 13),
(261, 4, 14),
(262, 4, 15),
(263, 4, 16),
(264, 4, 17),
(265, 1, 1),
(266, 1, 2),
(267, 1, 3),
(268, 1, 4),
(269, 1, 5),
(270, 1, 6),
(271, 1, 7),
(272, 1, 8),
(273, 1, 9),
(274, 1, 10),
(275, 1, 11),
(276, 1, 12),
(277, 1, 13),
(278, 1, 14),
(279, 1, 15),
(280, 1, 16),
(281, 1, 17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `movimiento` varchar(20) NOT NULL DEFAULT 'EGRESO',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id`, `id_venta`, `id_producto`, `nombre`, `cantidad`, `precio`, `movimiento`, `fecha_alta`) VALUES
(1, 1, 1, 'Coquito', 2, 5.00, 'EGRESO', '2024-10-07 20:35:39'),
(2, 2, 3, 'Pan', 1, 1.00, 'EGRESO', '2024-10-07 20:52:55'),
(3, 2, 2, 'Cola Cola', 1, 12.00, 'EGRESO', '2024-10-07 20:52:55'),
(4, 10, 3, 'Pan', 1, 1.00, 'EGRESO', '2024-10-07 21:04:08'),
(5, 19, 3, 'Pan', 1, 1.00, 'EGRESO', '2024-10-07 21:22:42'),
(6, 20, 2, 'Cola Cola', 1, 12.00, 'EGRESO', '2024-10-08 20:08:52'),
(7, 21, 2, 'Cola Cola', 1, 12.00, 'EGRESO', '2024-10-09 20:18:22'),
(8, 21, 4, 'carpicola', 1, 123.00, 'EGRESO', '2024-10-09 20:18:22'),
(9, 23, 3, 'Pan', 3, 1.50, 'EGRESO', '2024-10-09 21:02:23'),
(10, 24, 2, 'Cola Cola', 1, 12.00, 'EGRESO', '2024-10-11 02:08:21'),
(11, 25, 1, 'Coquito', 3, 10.00, 'EGRESO', '2024-10-11 02:13:18'),
(12, 25, 2, 'Cola Cola', 3, 22.00, 'EGRESO', '2024-10-11 02:13:18'),
(13, 25, 3, 'Pan', 2, 11.50, 'EGRESO', '2024-10-11 02:13:18'),
(14, 26, 1, 'Coquito', 3, 10.00, 'EGRESO', '2024-10-12 20:24:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `ci` varchar(15) NOT NULL,
  `nombres` varchar(25) NOT NULL,
  `ap` varchar(15) NOT NULL,
  `am` varchar(15) NOT NULL,
  `cel_ref` varchar(20) NOT NULL,
  `direccion` varchar(30) NOT NULL,
  `genero` varchar(20) NOT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_modifica` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `ci`, `nombres`, `ap`, `am`, `cel_ref`, `direccion`, `genero`, `activo`, `fecha_alta`, `fecha_modifica`) VALUES
(1, '12345', 'ALVARO', 'FLORES', 'MAMANI', '60257790', 'BARRIO LOS ALAMOS', 'MASCULINO', 1, '2024-10-03 19:33:04', '2024-10-03 19:33:04'),
(2, '12345671', 'mario', 'martin', 'miriam', '12343', 'Calle los olivos', 'MASCULINO', 1, '2024-10-08 21:00:54', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `tipo`) VALUES
(1, 'ListaProductos', 1),
(2, 'ListaProveedores', 2),
(3, 'ProveedoresEliminar', 2),
(4, 'ListaCategorias', 3),
(5, 'CategoriasEliminar', 3),
(6, 'NuevaCompra', 4),
(7, 'HistorialCompra', 5),
(8, 'Caja', 6),
(9, 'HistorialVentas', 7),
(10, 'EliminarVentas', 7),
(11, 'ListaClientes', 8),
(12, 'ArqueoCaja', 9),
(13, 'Reportes', 10),
(14, 'ListaUsuarios', 11),
(15, 'ListaEmpleados', 12),
(16, 'ListaRoles', 13),
(17, 'Configuracion', 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `existencias` int(11) NOT NULL,
  `stock_minimo` int(11) NOT NULL,
  `movimiento` varchar(20) NOT NULL DEFAULT 'Entrada Inicial',
  `fecha_vence` date NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_edit` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `codigo`, `nombre`, `precio_venta`, `precio_compra`, `existencias`, `stock_minimo`, `movimiento`, `fecha_vence`, `id_proveedor`, `id_categoria`, `activo`, `fecha_alta`, `fecha_edit`) VALUES
(1, '123', 'Coquito', 10.00, 7.00, 94, 8, 'Inicial', '2024-02-16', 2, 2, 1, '2024-10-12 21:07:06', '2024-04-18 13:14:15'),
(2, '1234', 'Cola Cola', 22.00, 15.00, 110, 5, '1', '2024-11-23', 2, 1, 1, '2024-10-11 02:13:18', '2024-04-18 13:15:24'),
(3, '12345', 'Pan', 11.50, 2.00, 99, 9, '1', '2025-01-16', 3, 2, 1, '2024-10-11 02:13:18', '2024-04-18 14:21:12'),
(4, '7772115250023', 'carpicola', 12.00, 11.00, 101, 1, '1', '2024-11-30', 1, 1, 1, '2024-10-11 02:11:51', NULL),
(5, '123435', 'Sardina', 17.00, 15.00, 12, 10, '', '2024-10-25', 2, 3, 1, '2024-10-12 21:21:55', NULL),
(6, '12368', 'azucar', 14.00, 12.00, 10, 1, 'Entrada Inicial', '2024-11-29', 5, 6, 1, '2024-10-12 21:45:20', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `apellido` varchar(20) NOT NULL,
  `CI` varchar(15) NOT NULL,
  `cel_ref` varchar(15) NOT NULL,
  `direccion` varchar(30) NOT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_modifica` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre`, `apellido`, `CI`, `cel_ref`, `direccion`, `activo`, `fecha_alta`, `fecha_modifica`) VALUES
(1, 'Marco', 'Valdez', '554321', '6741620', 'Calle Dinamarca', 1, '2024-10-03 19:33:04', '2024-10-03 19:33:04'),
(2, 'Liam', 'Ibarra', '654321', '6790431', 'Calle Holanda', 1, '2024-10-03 19:33:04', '2024-10-03 19:33:04'),
(3, 'Julio', 'Ponce', '433244', '646435', 'Barrio Torrez', 1, '2024-10-03 19:33:04', '2024-10-03 19:33:04'),
(4, 'Sarahi', 'Mejia', '745621', '388923', 'Barrio Wacner', 1, '2024-10-03 19:33:04', '2024-10-03 19:33:04'),
(5, 'Fito', 'Lopez', '653456', '335631', 'Calle Italia', 1, '2024-10-03 19:33:04', '2024-10-03 19:33:04'),
(6, 'Gabriel', 'Camacho', '7654321', '6842422', 'Calle Ingavi', 1, '2024-10-08 20:34:06', '2024-10-03 19:33:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_modifica` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `activo`, `fecha_alta`, `fecha_modifica`) VALUES
(1, 'Administrador', 1, '2024-05-16 05:26:23', NULL),
(2, 'Cajero', 1, '2024-05-16 05:26:23', NULL),
(3, 'Cajero2', 1, '2024-06-16 05:26:23', NULL),
(4, 'Cajero3', 1, '2024-06-16 05:26:23', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temporal_compra`
--

CREATE TABLE `temporal_compra` (
  `id` int(11) NOT NULL,
  `folio` varchar(15) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `temporal_compra`
--

INSERT INTO `temporal_compra` (`id`, `folio`, `id_producto`, `codigo`, `nombre`, `cantidad`, `precio`, `subtotal`) VALUES
(7, '670adbfe22ac4', 1, '123', 'Coquito', 1, 7.00, 7.00),
(8, '670ae14307c58', 1, '123', 'Coquito', 1, 7.00, 7.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temporal_venta`
--

CREATE TABLE `temporal_venta` (
  `id` int(11) NOT NULL,
  `folio` varchar(15) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `temporal_venta`
--

INSERT INTO `temporal_venta` (`id`, `folio`, `id_producto`, `codigo`, `nombre`, `cantidad`, `precio`, `subtotal`) VALUES
(4, '67044b6c56ff2', 1, '123', 'Coquito', 1, 5.00, 5.00),
(5, '67044b86dbf11', 2, '1234', 'Cola Cola', 1, 12.00, 12.00),
(6, '67044b902f45e', 2, '1234', 'Cola Cola', 1, 12.00, 12.00),
(7, '67044bdf8f95a', 1, '123', 'Coquito', 1, 5.00, 5.00),
(8, '67044be61f843', 2, '1234', 'Cola Cola', 1, 12.00, 12.00),
(10, '67044cd5b634a', 1, '123', 'Coquito', 1, 5.00, 5.00),
(11, '67044d1760dfe', 1, '123', 'Coquito', 1, 5.00, 5.00),
(12, '67044da484247', 1, '123', 'Coquito', 1, 5.00, 5.00),
(13, '67044e7062b81', 1, '123', 'Coquito', 1, 5.00, 5.00),
(14, '67044eba2560d', 1, '123', 'Coquito', 1, 5.00, 5.00),
(15, '67044f7b5f3b5', 1, '123', 'Coquito', 1, 5.00, 5.00),
(16, '67044fc0e0ea2', 1, '123', 'Coquito', 1, 5.00, 5.00),
(17, '670450fb90abd', 1, '123', 'Coquito', 1, 5.00, 5.00),
(20, '6706d8b35afee', 1, '123', 'Coquito', 1, 5.00, 5.00),
(21, '6706dd33e516f', 4, '7772115250023', 'carpicola', 1, 123.00, 123.00),
(22, '6706dd5d65b98', 1, '123', 'Coquito', 1, 5.00, 5.00),
(23, '6706df50e3c04', 1, '123', 'Coquito', 2, 5.00, 10.00),
(24, '6706df50e3c04', 2, '1234', 'Cola Cola', 1, 12.00, 12.00),
(25, '6706dfd7dc031', 1, '123', 'Coquito', 1, 5.00, 5.00),
(26, '6706e077a5998', 1, '123', 'Coquito', 2, 5.00, 10.00),
(27, '6706e077a5998', 3, '12345', 'Pan', 2, 1.00, 2.00),
(28, '6706e077a5998', 4, '7772115250023', 'carpicola', 2, 123.00, 246.00),
(29, '6706e0af1145c', 1, '123', 'Coquito', 1, 5.00, 5.00),
(30, '6706e0b655619', 1, '123', 'Coquito', 1, 5.00, 5.00),
(31, '6706e0bef27c4', 1, '123', 'Coquito', 1, 5.00, 5.00),
(32, '6706e0bef27c4', 2, '1234', 'Cola Cola', 1, 12.00, 12.00),
(33, '6706e0bef27c4', 3, '12345', 'Pan', 1, 1.00, 1.00),
(34, '6706e0bef27c4', 4, '7772115250023', 'carpicola', 4, 123.00, 492.00),
(35, '6706e2b7bf7a6', 1, '123', 'Coquito', 1, 5.00, 5.00),
(36, '6706e3256d808', 1, '123', 'Coquito', 1, 5.00, 5.00),
(39, '6706e51278cf2', 3, '12345', 'Pan', 1, 1.00, 1.00),
(40, '6706e61a79323', 4, '7772115250023', 'carpicola', 6, 123.00, 738.00),
(41, '6706e63fe9839', 4, '7772115250023', 'carpicola', 4, 123.00, 492.00),
(42, '6706e8508a5cd', 3, '12345', 'Pan', 1, 1.50, 1.50),
(45, '67088915d6948', 1, '123', 'Coquito', 1, 10.00, 10.00),
(49, '670adb04c6187', 1, '123', 'Coquito', 2, 10.00, 20.00),
(51, '670ae1e2955c3', 1, '123', 'Coquito', 2, 10.00, 20.00),
(52, '670ae5194a471', 1, '123', 'Coquito', 1, 10.00, 10.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(30) NOT NULL,
  `password` varchar(130) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `id_caja` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_modifica` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `password`, `id_empleado`, `id_caja`, `id_rol`, `activo`, `fecha_alta`, `fecha_modifica`) VALUES
(1, 'alvaro', '$2y$10$9RmbV/ie1DkewcivBwTtCekhYPgmRWFTvxygjj8kt9WiEW0cveVye', 1, 1, 1, 1, '2024-05-21 22:34:10', '2024-05-21 22:34:10'),
(2, 'Mario123', '$2y$10$jLT7OKupiHMP6OQWpclHXuSc0DG5XxLLsy7i/SSas3KLk1H5lVpoy', 2, 2, 2, 1, '2024-10-08 21:01:57', '2024-10-08 21:01:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `folio` varchar(15) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_usuario` int(11) NOT NULL,
  `id_caja` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `forma_pago` varchar(5) NOT NULL,
  `activo` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `folio`, `total`, `fecha_alta`, `id_usuario`, `id_caja`, `id_cliente`, `forma_pago`, `activo`) VALUES
(1, '1', 10.00, '2024-10-07 20:35:39', 1, 1, 1, '001', 1),
(2, '2', 13.00, '2024-10-07 20:52:55', 1, 1, 1, '001', 1),
(3, '3', 5.00, '2024-10-07 20:58:28', 1, 1, 1, '001', 1),
(4, '4', 12.00, '2024-10-07 20:58:54', 1, 1, 1, '001', 1),
(5, '5', 12.00, '2024-10-07 20:59:00', 1, 1, 1, '001', 1),
(6, '6', 5.00, '2024-10-07 21:00:20', 1, 1, 1, '001', 1),
(7, '7', 12.00, '2024-10-07 21:00:26', 1, 1, 1, '001', 1),
(8, '8', 1.00, '2024-10-07 21:00:40', 1, 1, 1, '001', 1),
(9, '9', 1.00, '2024-10-07 21:01:25', 1, 1, 1, '001', 1),
(10, '10', 1.00, '2024-10-07 21:04:08', 1, 1, 1, '001', 1),
(11, '11', 5.00, '2024-10-07 21:04:28', 1, 1, 1, '001', 1),
(12, '12', 5.00, '2024-10-07 21:05:34', 1, 1, 1, '001', 1),
(13, '13', 5.00, '2024-10-07 21:07:52', 1, 1, 1, '001', 1),
(14, '14', 5.00, '2024-10-07 21:11:15', 1, 1, 1, '001', 1),
(15, '15', 5.00, '2024-10-07 21:12:33', 1, 1, 1, '001', 1),
(16, '16', 5.00, '2024-10-07 21:15:43', 1, 1, 1, '001', 1),
(17, '17', 5.00, '2024-10-07 21:16:52', 1, 1, 1, '001', 1),
(18, '18', 5.00, '2024-10-07 21:22:34', 1, 1, 1, '001', 1),
(19, '19', 1.00, '2024-10-07 21:22:42', 1, 1, 1, '001', 1),
(20, '20', 12.00, '2024-10-08 20:08:52', 1, 1, 1, '001', 1),
(21, '21', 135.00, '2024-10-09 20:18:22', 1, 1, 1, '001', 1),
(22, '22', 492.00, '2024-10-09 20:23:45', 1, 1, 1, '001', 1),
(23, '23', 4.50, '2024-10-09 21:02:23', 1, 1, 1, '001', 1),
(24, '24', 12.00, '2024-10-11 02:08:21', 1, 1, 1, '001', 1),
(25, '25', 119.00, '2024-10-11 02:13:18', 1, 1, 1, '001', 1),
(26, '26', 30.00, '2024-10-12 20:24:57', 1, 1, 1, '001', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `arqueo_caja`
--
ALTER TABLE `arqueo_caja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_caja` (`id_caja`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `cajas`
--
ALTER TABLE `cajas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `CI` (`CI`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_compra` (`id_compra`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_roles_permisos`
--
ALTER TABLE `detalle_roles_permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ci` (`ci`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `id_proveedor` (`id_proveedor`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `CI` (`CI`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `temporal_compra`
--
ALTER TABLE `temporal_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `temporal_venta`
--
ALTER TABLE `temporal_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_empleado` (`id_empleado`),
  ADD KEY `id_caja` (`id_caja`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_caja` (`id_caja`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `arqueo_caja`
--
ALTER TABLE `arqueo_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cajas`
--
ALTER TABLE `cajas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detalle_roles_permisos`
--
ALTER TABLE `detalle_roles_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `temporal_compra`
--
ALTER TABLE `temporal_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `temporal_venta`
--
ALTER TABLE `temporal_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `arqueo_caja`
--
ALTER TABLE `arqueo_caja`
  ADD CONSTRAINT `arqueo_caja_ibfk_1` FOREIGN KEY (`id_caja`) REFERENCES `cajas` (`id`),
  ADD CONSTRAINT `arqueo_caja_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `detalle_compra_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id`),
  ADD CONSTRAINT `detalle_compra_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `detalle_roles_permisos`
--
ALTER TABLE `detalle_roles_permisos`
  ADD CONSTRAINT `detalle_roles_permisos_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `detalle_roles_permisos_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id`);

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `temporal_compra`
--
ALTER TABLE `temporal_compra`
  ADD CONSTRAINT `temporal_compra_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `temporal_venta`
--
ALTER TABLE `temporal_venta`
  ADD CONSTRAINT `temporal_venta_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_caja`) REFERENCES `cajas` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`id_caja`) REFERENCES `cajas` (`id`),
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
