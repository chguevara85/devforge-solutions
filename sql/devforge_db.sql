-- =====================================================
-- BASE DE DATOS: DevForge Solutions
-- Motor: MySQL
-- =====================================================

CREATE DATABASE IF NOT EXISTS devforge_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE devforge_db;

-- =====================================================
-- TABLA: roles
-- =====================================================
CREATE TABLE IF NOT EXISTS roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(150),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- TABLA: usuarios
-- =====================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

-- =====================================================
-- TABLA: clientes
-- =====================================================
CREATE TABLE IF NOT EXISTS clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    razon_social VARCHAR(150) NOT NULL,
    ruc VARCHAR(11) UNIQUE,
    telefono VARCHAR(15),
    email VARCHAR(100),
    direccion VARCHAR(200),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- TABLA: proyectos
-- =====================================================
CREATE TABLE IF NOT EXISTS proyectos (
    id_proyecto INT AUTO_INCREMENT PRIMARY KEY,
    nombre_proyecto VARCHAR(150) NOT NULL,
    descripcion TEXT,
    id_cliente INT NOT NULL,
    id_responsable INT NOT NULL,
    estado ENUM('cotizacion', 'desarrollo', 'pruebas', 'entregado') DEFAULT 'cotizacion',
    fecha_inicio DATE,
    fecha_fin DATE,
    presupuesto DECIMAL(10,2),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
    FOREIGN KEY (id_responsable) REFERENCES usuarios(id_usuario)
);

-- =====================================================
-- TABLA: cotizaciones
-- =====================================================
CREATE TABLE IF NOT EXISTS cotizaciones (
    id_cotizacion INT AUTO_INCREMENT PRIMARY KEY,
    id_proyecto INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    detalle TEXT,
    estado ENUM('pendiente', 'aprobada', 'rechazada') DEFAULT 'pendiente',
    fecha_emision DATE NOT NULL,
    fecha_validez DATE,
    FOREIGN KEY (id_proyecto) REFERENCES proyectos(id_proyecto)
);

-- =====================================================
-- TABLA: reportes
-- =====================================================
CREATE TABLE IF NOT EXISTS reportes (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    tipo ENUM('proyectos', 'clientes', 'ingresos', 'general') NOT NULL,
    descripcion TEXT,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- =====================================================
-- DATOS INICIALES
-- =====================================================
INSERT IGNORE INTO roles (nombre_rol, descripcion) VALUES
('Administrador', 'Acceso total al sistema'),
('Desarrollador', 'Acceso a proyectos asignados'),
('Cliente', 'Acceso limitado a sus proyectos');