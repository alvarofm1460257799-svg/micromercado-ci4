-- CREATE DATABASE micromercado;

CREATE TABLE cajas (
  id int(11) NOT NULL AUTO_INCREMENT,
  numero_caja varchar(10) NOT NULL,
  nombre varchar(40) NOT NULL,
  folio int(11) NOT NULL,
  activo tinyint(4) NOT NULL DEFAULT 1,
  fecha_alta timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  fecha_modifica timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO cajas (id, numero_caja, nombre, folio, activo, fecha_alta, fecha_modifica) VALUES
(1, '1', 'Caja general', 1, 1, '2024-09-17 22:35:39', '2024-09-17 22:35:39'),
(2, '2', 'Caja secundaria', 2, 1, '2024-05-16 01:25:32', NULL);





CREATE TABLE clientes (
  id int(11) NOT NULL AUTO_INCREMENT,
  nombre varchar(50) NOT NULL,
  CI VARCHAR(15) NOT NULL UNIQUE,
  direccion varchar(100),
  telefono varchar(20),
  correo varchar(50),
  activo tinyint(4) NOT NULL DEFAULT 1,
  fecha_alta timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  fecha_edit timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO clientes (id, nombre, CI, direccion, telefono, correo, activo, fecha_alta, fecha_edit) VALUES
(1, 'Publico en general','11111111', 'Calle Barrio', '77777777', 'tienda@gmail.com', 1, '2024-04-23 10:02:21', '2024-04-23 09:02:21');



CREATE TABLE configuracion (
  id int(11) NOT NULL AUTO_INCREMENT,
  nombre varchar(50) NOT NULL,
  valor varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO configuracion (id, nombre, valor) VALUES
(1, 'tienda_nombre', 'MICROMERCADO GOLOSO '),
(2, 'tienda_rfc', 'XXAXX000000XXX'),
(3, 'tienda_telefono', '60257922'),
(4, 'tienda_email', 'tienda123@gmail.com'),
(5, 'tienda_direccion', 'AVENIDA  SUCRE\r\n'),
(6, 'ticket_leyenda', 'Gracias por comprar');



CREATE TABLE proveedores (
  id int(11) NOT NULL AUTO_INCREMENT,
  empresa VARCHAR(30) NOT NULL,
  nombre VARCHAR(20) NOT NULL,
  apellido VARCHAR(20) NOT NULL,
  CI VARCHAR(15) NOT NULL UNIQUE,
  cel_ref VARCHAR(15) NOT NULL,
  direccion VARCHAR(50)NOT NULL,
  activo tinyint(4) NOT NULL DEFAULT 1,
  fecha_alta timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  fecha_modifica timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

CREATE TABLE categorias (
  id  int(11) NOT NULL AUTO_INCREMENT,
  nombre varchar(100) NOT NULL,
  dias_aviso INT NOT NULL,
  activo tinyint(4) NOT NULL DEFAULT 1,
  fecha_alta timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  fecha_edit timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

CREATE TABLE productos (
  id int(11) NOT NULL AUTO_INCREMENT,
  codigo varchar(20) NOT NULL UNIQUE,
  nombre varchar(120) NOT NULL UNIQUE,
  precio_venta decimal(10,2) NOT NULL,
  precio_compra decimal(10,2) NOT NULL,
  existencias int(11) NOT NULL ,
  stock_minimo int(11) NOT NULL ,
  id_proveedor int(11) NOT NULL,
  id_categoria int(11) NOT NULL,
  activo tinyint(4) NOT NULL DEFAULT 1,
  fecha_alta timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  fecha_edit timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (id_proveedor) REFERENCES proveedores(id),  
  FOREIGN KEY (id_categoria) REFERENCES categorias(id)    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;










CREATE TABLE permisos (
  id int(11) NOT NULL AUTO_INCREMENT,
  nombre varchar(30) NOT NULL,
  tipo int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO permisos (id, nombre, tipo) VALUES
(1, 'GESTION PRODUCTOS', 1),
(2, '___Lista_productos', 2),
(3, '___Lista_proveedores', 3),
(4, '___Eliminar_proveedores', 3),
(5, '___Lista_categorizada', 4),
(6, '___Eliminar_categorizacion', 4),
(7, 'MOVIMIENTO STOCK', 5),
(8, 'GESTION COMPRAS', 6),
(9, '___Nueva_compra', 7),
(10, '___Historial_compra', 8), --
(11, 'ATENCION CAJA', 9),
(12, 'GESTION VENTAS', 10),
(13, '___Historial_ventas', 11),
(14, '___Lista_clientes', 12),
(15, '___Eliminar_clientes', 12),
(16, '___Arqueo_cajas', 13),
(17, 'REPORTES', 14),
(18, '___Reporte_productos', 15),
(19, '___Reporte_ventas', 16),
(20, '___Reporte_compras', 17),
(21, 'GESTION USUARIOS', 18),
(22, '___Lista_usuarios',19),
(23, '___Eliminar_usuarios',19),
(24, '___Lista_personal',20),
(25, '___Roles_accesos',21),
(26, 'CONFIGURACION',22);


-- --------------------------------------------------------



CREATE TABLE roles (
  id int(11) NOT NULL AUTO_INCREMENT,
  nombre varchar(50) NOT NULL,
  activo tinyint(4) NOT NULL DEFAULT 1,
  fecha_alta timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  fecha_modifica timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO roles (id, nombre, activo, fecha_alta, fecha_modifica) VALUES
(1, 'Administrador', 1, '2024-05-16 01:26:23', NULL),
(2, 'Cajero', 1, '2024-05-16 01:26:23', NULL);



-- ojoooooooooooooooooooooooooooooooooooooooooooooooo


CREATE TABLE detalle_roles_permisos (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_rol int(11) NOT NULL,
  id_permiso int(11) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY(id_rol)REFERENCES roles(id),
  FOREIGN KEY(id_permiso)REFERENCES permisos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO detalle_roles_permisos (id, id_rol, id_permiso) VALUES
(197, 1, 1),
(198, 1, 2),
(199, 1, 3),
(200, 1, 4),
(201, 1, 5),
(202, 1, 6),
(203, 1, 7),
(204, 1, 8),
(205, 1, 9),
(206, 1, 10),
(207, 1, 11),
(208, 1, 12),
(209, 1, 13),
(210, 1, 14),
(211, 1, 15),
(212, 1, 16),
(213, 1, 17),
(214, 1, 18),
(215, 1, 19),
(216, 1, 20),
(217, 1, 21),
(218, 1, 22),
(219, 1, 23),
(220, 1, 24),
(221, 1, 25),
(222, 1, 26),
(223, 2, 1),
(224, 2, 2),
(225, 2, 3),
(226, 2, 4),
(227, 2, 5),
(228, 2, 6),
(229, 2, 7),
(230, 2, 8),
(231, 2, 9),
(232, 2, 10),
(233, 2, 11),
(234, 2, 12),
(235, 2, 13),
(236, 2, 14),
(237, 2, 15),
(238, 2, 16),
(239, 2, 17),
(240, 2, 18);





-- ----------------------------------------------
CREATE TABLE empleados (
    id INT NOT NULL AUTO_INCREMENT,
    ci VARCHAR(15) NOT NULL UNIQUE,
    nombres VARCHAR(25) NOT NULL,
    ap VARCHAR(25) NOT NULL,
    am VARCHAR(25) NOT NULL,
    cel_ref VARCHAR(10) NOT NULL,
    direccion VARCHAR(50) NOT NULL,
    genero VARCHAR(20) NOT NULL,
    activo tinyint(4) NOT NULL DEFAULT 1,
    fecha_alta timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    fecha_modifica timestamp NULL DEFAULT NULL,
    PRIMARY KEY(id)
) ENGINE=INNODB;

INSERT INTO empleados (ci, nombres, ap, am, cel_ref, direccion, genero, activo, fecha_alta, fecha_modifica) VALUES
('12345', 'ALVARO', 'FLORES', 'MAMANI', '60257790', 'BARRIO LOS ALAMOS', 'MASCULINO', 1, '2024-04-01 08:00:00', '2024-04-01 08:00:00'),
('67890', 'MARCELA', 'PEREZ', 'LOPEZ', '70123456', 'CALLE 1 ZONA CENTRAL', 'FEMENINO', 1, '2024-04-02 09:15:00', '2024-04-02 09:15:00'),
('54321', 'JUAN', 'GUTIERREZ', 'CUEVAS', '71234567', 'CALLE 3 ZONA SUR', 'MASCULINO', 1, '2024-04-03 10:30:00', '2024-04-03 10:30:00'),
('98765', 'KARLA', 'VELASQUEZ', 'MARTINEZ', '73211234', 'AVENIDA BOLIVAR', 'FEMENINO', 1, '2024-04-04 11:45:00', '2024-04-04 11:45:00'),
('45678', 'GABRIEL', 'MENDOZA', 'ARIAS', '71234568', 'BARRIO ALTO SAN PEDRO', 'MASCULINO', 1, '2024-04-05 08:30:00', '2024-04-05 08:30:00'),
('11223', 'SOFIA', 'GONZALEZ', 'RIVERA', '70987654', 'CALLE INGAVI', 'FEMENINO', 1, '2024-04-06 09:45:00', '2024-04-06 09:45:00'),
('33445', 'MARIO', 'QUISPE', 'CHAVEZ', '78965412', 'ZONA NORTE', 'MASCULINO', 1, '2024-04-07 10:00:00', '2024-04-07 10:00:00'),
('99887', 'PATRICIA', 'VARGAS', 'CARRASCO', '74561234', 'AV. LAS AMERICAS', 'FEMENINO', 1, '2024-04-08 11:00:00', '2024-04-08 11:00:00'),
('66789', 'ALEJANDRO', 'CRUZ', 'PEREIRA', '75647890', 'BARRIO CENTRAL', 'MASCULINO', 1, '2024-04-09 12:30:00', '2024-04-09 12:30:00'),
('44556', 'ELENA', 'ORTIZ', 'GUTIERREZ', '74896523', 'CALLE LOPEZ', 'FEMENINO', 1, '2024-04-10 08:15:00', '2024-04-10 08:15:00');





CREATE TABLE usuarios (
  id int(11) NOT NULL AUTO_INCREMENT,
  usuario varchar(30) NOT NULL,
  password varchar(130) NOT NULL,
  id_empleado int(11) NOT NULL,
  id_caja int(11) NOT NULL,
  id_rol int(11) NOT NULL,
  activo tinyint(4) NOT NULL DEFAULT 1,
  fecha_alta timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  fecha_modifica timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY(id_empleado)REFERENCES empleados(id),
  FOREIGN KEY(id_caja)REFERENCES cajas(id),
  FOREIGN KEY(id_rol)REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO usuarios (id, usuario, password, id_empleado, id_caja, id_rol, activo, fecha_alta, fecha_modifica) VALUES

(1, 'alvaro', '$2y$10$9RmbV/ie1DkewcivBwTtCekhYPgmRWFTvxygjj8kt9WiEW0cveVye', 1, 1, 1, 1, '2024-05-21 18:34:10', '2024-05-21 18:34:10');


-- Crear tabla con jerarquía
CREATE TABLE presentaciones_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    codigo varchar(20) NOT NULL UNIQUE,
    tipo VARCHAR(50) NOT NULL, -- ejemplo: 'unidad', 'paquete 6', 'caja 24'
    cantidad_unidades INT NOT NULL DEFAULT 1,
    precio_venta DECIMAL(10,2) NOT NULL,
    precio_compra DECIMAL(10,2) DEFAULT 0,
    id_padre INT DEFAULT NULL, -- Jerarquía
    activo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (id_producto) REFERENCES productos(id),
    FOREIGN KEY (id_padre) REFERENCES presentaciones_productos(id)
);





CREATE TABLE ventas (
  id int(11) NOT NULL AUTO_INCREMENT,

  folio varchar(15) NOT NULL,
  total decimal(10,2) NOT NULL,
  fecha_alta timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  id_usuario int(11) NOT NULL,
  id_caja int(11) NOT NULL,
  id_cliente int(11) NOT NULL,

  forma_pago varchar(5) NOT NULL,
  activo tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  FOREIGN KEY(id_usuario)REFERENCES usuarios(id),
  FOREIGN KEY(id_caja)REFERENCES cajas(id),
  FOREIGN KEY(id_cliente)REFERENCES clientes(id)


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE compras (
  id int(11) NOT NULL AUTO_INCREMENT,
  folio varchar(15) NOT NULL,
  total decimal(10,2) NOT NULL,
  id_usuario int(11) NOT NULL,
  activo tinyint(11) NOT NULL DEFAULT 1,
  fecha_alta timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  FOREIGN KEY(id_usuario)REFERENCES usuarios(id)

) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



CREATE TABLE detalle_compra (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_compra int(11) NOT NULL,
  id_producto int(11) NOT NULL,
  id_presentacion int(11) NOT NULL,
  nombre varchar(200) NOT NULL,
  cantidad int(11) NOT NULL,
  cantidad_mayor DECIMAL(10,2) NULL,      -- Nueva columna
  precio decimal(10,2) NOT NULL,
  movimiento VARCHAR(20) NOT NULL DEFAULT 'COMPRAS',
  fecha_alta timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  FOREIGN KEY(id_compra) REFERENCES compras(id),
  FOREIGN KEY(id_producto) REFERENCES productos(id),

  FOREIGN KEY(id_presentacion) REFERENCES presentaciones_productos(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE lotes_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    id_detalle_compra INT NULL,          -- enlace al detalle de compra
    fecha_vencimiento DATE,
    cantidad INT NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro DATETIME NOT NULL,
    movimiento VARCHAR(30),
    CONSTRAINT fk_lotes_producto FOREIGN KEY (id_producto) REFERENCES productos(id),
    CONSTRAINT fk_lotes_detalle_compra FOREIGN KEY (id_detalle_compra) REFERENCES detalle_compra(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE detalle_venta (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_producto int(11) NOT NULL,
  id_venta int(11) NOT NULL,
  id_lote int(11) NOT NULL,
  id_presentacion int(11) NOT NULL,
  nombre VARCHAR(30) NOT NULL,
  cantidad int(11) NOT NULL,
  cantidad_mayor DECIMAL(10,2) NULL,  
  precio decimal(10,2) NOT NULL,
  precio_compra  decimal(10,2) NOT NULL,
  movimiento VARCHAR(20) NOT NULL DEFAULT 'VENTAS',  -- Valor por defecto
  fecha_alta timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id),
  FOREIGN KEY(id_venta)REFERENCES ventas(id),
  FOREIGN KEY(id_producto)REFERENCES productos(id),
  FOREIGN KEY(id_lote)REFERENCES lotes_productos(id),
  FOREIGN KEY(id_presentacion)REFERENCES presentaciones_productos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;





CREATE TABLE temporal_venta (
  id int(11) NOT NULL AUTO_INCREMENT,
  folio varchar(15) NOT NULL,
  id_producto int(11) NOT NULL,
  id_lote int(11) NOT NULL,
  id_presentacion int(11) NOT NULL,
  codigo varchar(20) NOT NULL,
  nombre varchar(200) NOT NULL,
  cantidad int(11) NOT NULL,
  cantidad_mayor DECIMAL(10,2),
  precio decimal(10,2) NOT NULL,
  subtotal decimal(10,2) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY(id_producto)REFERENCES productos(id),
  FOREIGN KEY(id_lote)REFERENCES lotes_productos(id),
  FOREIGN KEY(id_presentacion)REFERENCES presentaciones_productos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;





CREATE TABLE temporal_compra (
  id int(11) NOT NULL AUTO_INCREMENT,
  folio varchar(15) NOT NULL,
  id_producto int(11) NOT NULL,
  id_lote int(11),
  id_presentacion int(11) NOT NULL,
  codigo varchar(20) NOT NULL,
  nombre varchar(200) NOT NULL,
  cantidad int(11) NOT NULL,
  cantidad_mayor DECIMAL(10,2),
  precio_compra decimal(10,2) NOT NULL,
  precio_venta decimal(10,2) NOT NULL,
  precio_compra_m decimal(10,2) NOT NULL,
  precio_venta_m decimal(10,2) NOT NULL,
  subtotal decimal(10,2) NOT NULL,
  fecha_vence DATE NOT NULL, -- Nueva columna con el nombre solicitado
  PRIMARY KEY (id),
  FOREIGN KEY (id_producto) REFERENCES productos(id),
  FOREIGN KEY(id_lote)REFERENCES lotes_productos(id),
  FOREIGN KEY(id_presentacion)REFERENCES presentaciones_productos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;








CREATE TABLE arqueo_caja (
  id int(11) NOT NULL AUTO_INCREMENT,
  id_caja int(11) NOT NULL,
  id_usuario int(11) NOT NULL,
  fecha_inicio datetime NOT NULL,
  fecha_fin datetime DEFAULT NULL,
  monto_inicial decimal(10,2) NOT NULL,
  monto_final decimal(10,2) DEFAULT NULL,
  total_ventas int(11) NOT NULL,
  estatus int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  FOREIGN KEY(id_caja)REFERENCES cajas(id),
  FOREIGN KEY(id_usuario)REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE ventas_sin_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    nombre_producto VARCHAR(255),
    cantidad_faltante DECIMAL(10,2),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE variantes_producto (
  id INT(11) NOT NULL AUTO_INCREMENT,
  id_producto INT(11) NOT NULL,
  codigo_barra VARCHAR(20) NOT NULL UNIQUE,
  descripcion VARCHAR(50) NOT NULL, -- por ejemplo: “Fresa”, “Vainilla”
  activo TINYINT(4) NOT NULL DEFAULT 1,
  fecha_alta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  fecha_edit TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (id_producto) REFERENCES productos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

CREATE TABLE ajustes_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    motivo VARCHAR(255) NOT NULL,
    observaciones TEXT NULL,
    id_usuario INT NULL,
    CONSTRAINT fk_ajuste_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE detalle_ajuste_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_ajuste INT NOT NULL,
    id_producto INT NOT NULL,
    id_lote INT NULL,  -- opcional: si el ajuste es lote específico
    cantidad_antes DECIMAL(10,2) NOT NULL,
    cantidad_despues DECIMAL(10,2) NOT NULL,
    diferencia DECIMAL(10,2) NOT NULL,
    observacion VARCHAR(255) NULL,
    CONSTRAINT fk_detalle_ajuste FOREIGN KEY (id_ajuste) REFERENCES ajustes_inventario(id) ON DELETE CASCADE,
    CONSTRAINT fk_detalle_ajuste_producto FOREIGN KEY (id_producto) REFERENCES productos(id),
    CONSTRAINT fk_detalle_ajuste_lote FOREIGN KEY (id_lote) REFERENCES lotes_productos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE movimientos_extra (
  id INT(11) NOT NULL AUTO_INCREMENT,
  tipo ENUM('INGRESO','GASTO') NOT NULL,
  descripcion VARCHAR(150) NOT NULL,
  monto DECIMAL(10,2) NOT NULL,
  categoria ENUM('Servicios','Transporte','Mantenimiento','Aporte','Devolucion','Prestamo','Otros') DEFAULT 'Otros',
  id_usuario INT(11) NOT NULL,
  fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  activo TINYINT(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
