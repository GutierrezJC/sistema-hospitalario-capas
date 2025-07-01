-- Archivo: base_datos.sql

CREATE DATABASE IF NOT EXISTS hospital
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_spanish_ci;

USE hospital;

CREATE TABLE IF NOT EXISTS usuario (
  id_Usuario INT AUTO_INCREMENT PRIMARY KEY,
  identificacion VARCHAR(50) NOT NULL UNIQUE,
  correo VARCHAR(100) NOT NULL,
  rol VARCHAR(20) NOT NULL,
  passw VARCHAR(255),
  ultimo_acceso DATETIME,
  tkRef varchar(255) DEFAULT NULL -- Token de referencia para la sesión
);

CREATE TABLE IF NOT EXISTS visitante (
  id_Visitante INT AUTO_INCREMENT PRIMARY KEY,
  identificacion VARCHAR(20) NOT NULL UNIQUE,
  nombre VARCHAR(30) NOT NULL,
  apellido1 VARCHAR(30) NOT NULL,
  apellido2 VARCHAR(30) NOT NULL,
  telefono VARCHAR(15) NOT NULL,
  correo VARCHAR(100) ,
  sector_laboral VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS administrador (
  id_Administrador INT AUTO_INCREMENT PRIMARY KEY,
  identificacion VARCHAR(20) NOT NULL UNIQUE,
  nombre VARCHAR(30) NOT NULL,
  apellido1 VARCHAR(15) NOT NULL,
  apellido2 VARCHAR(15) NOT NULL,
  telefono VARCHAR(9) NOT NULL,
  celular VARCHAR(9),
  direccion VARCHAR(255),
  correo VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS visita (
  id_visita INT AUTO_INCREMENT PRIMARY KEY,
  identificacion_visitante VARCHAR(20) NOT NULL,      -- Aquí guardas la cédula del visitante
  identificacion_administrador VARCHAR(20) NOT NULL,  -- Aquí guardas la cédula del admin
  motivo_visita VARCHAR(255) NOT NULL,
  fecha_entrada DATETIME NOT NULL,
  fecha_salida DATETIME ,
  estado ENUM('en curso', 'finalizada') NOT NULL DEFAULT 'en curso'
);

CREATE TABLE IF NOT EXISTS motivo_visita (
  id_motivo INT AUTO_INCREMENT PRIMARY KEY,
  descripcion VARCHAR(255) NOT NULL
);

-- -- Insertar 5 administradores
-- INSERT INTO administrador (identificacion, nombre, apellido1, apellido2, telefono, celular, direccion, correo) VALUES
-- ('1234567', 'Carlos', 'Mora', 'Jiménez', '22223333', '88889999', 'San José', 'carlos.mora@hospital.com'),
-- ('7654321', 'Ana', 'Gómez', 'Rojas', '22224444', '88887777', 'Alajuela', 'ana.gomez@hospital.com'),
-- ('1111111', 'Luis', 'Pérez', 'Soto', '22225555', '88886666', 'Cartago', 'luis.perez@hospital.com'),
-- ('2222222', 'María', 'Fernández', 'Vargas', '22226666', '88885555', 'Heredia', 'maria.fernandez@hospital.com'),
-- ('3333333', 'Jorge', 'Ramírez', 'Castro', '22227777', '88884444', 'Puntarenas', 'jorge.ramirez@hospital.com');

-- -- Insertar 5 visitantes
-- INSERT INTO visitante (identificacion, nombre, apellido1, apellido2, telefono, correo, sector_laboral) VALUES
-- ('702330571', 'Carlos', 'Mora', 'Jiménez', '88889999', 'carlos.mora@gmail.com', 'Tecnología'),
-- ('88888888', 'CILO', 'MODa', 'García', '88887777', 'cilo.moda@gmail.com', 'Educación'),
-- ('99999999', 'Lucía', 'Vega', 'Solís', '88886666', 'lucia.vega@gmail.com', 'Salud'),
-- ('55555555', 'Pedro', 'Sánchez', 'López', '88885555', 'pedro.sanchez@gmail.com', 'Construcción'),
-- ('44444444', 'Andrea', 'Campos', 'Martínez', '88884444', 'andrea.campos@gmail.com', 'Administración');

-- -- Insertar 10 visitas (todas las identificaciones existen en visitante y administrador)
-- INSERT INTO visita (identificacion_visitante, identificacion_administrador, motivo_visita, fecha_entrada, fecha_salida, estado) VALUES
-- ('702330571', '1234567', 'Reunión de trabajo', '2024-06-01 09:00:00', '2024-06-01 10:00:00', 'finalizada'),
-- ('702330571', '1234567', 'Entrega de documentos', '2024-06-02 11:00:00', '2024-06-02 11:30:00', 'finalizada'),
-- ('702330571', '1234567', 'Consulta médica', '2024-06-03 08:30:00', '2024-06-03 09:15:00', 'finalizada'),
-- ('702330571', '1234567', 'Capacitación', '2024-06-04 14:00:00', '2024-06-04 16:00:00', 'finalizada'),
-- ('702330571', '1234567', 'Supervisión', '2024-06-05 10:00:00', '2024-06-05 11:00:00', 'en curso'),
-- ('702330571', '7654321', 'Visita técnica', '2024-07-01 09:00:00', '2024-07-01 10:00:00', 'en curso'),
-- ('88888888', '7654321', 'Auditoría interna', '2024-07-02 11:00:00', '2024-07-02 12:00:00', 'finalizada'),
-- ('88888888', '1234567', 'Capacitación avanzada', '2024-07-03 08:30:00', '2024-07-03 09:30:00', 'finalizada'),
-- ('99999999', '2222222', 'Supervisión especial', '2024-07-04 14:00:00', '2024-07-04 15:00:00', 'en curso'),
-- ('55555555', '3333333', 'Reunión de coordinación', '2024-07-05 10:00:00', '2024-07-05 11:00:00', 'en curso');