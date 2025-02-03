CREATE DATABASE IF NOT EXISTS boardsgw;
USE boardsgw;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'usuario') NOT NULL,
    status ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo'
);

-- Insertar el usuario administrador (password: 123 hasheada)
INSERT INTO usuarios (usuario, password, rol, status) 
VALUES ('admin', '$2y$10$dtgRKNNXyGakTOgb9kFsCuYRexmZLYbsgIWvAB2kQU7jB5Ysq56a6', 'administrador', 'activo'); 

CREATE TABLE boards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT,
    creado_por INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creado_por) REFERENCES usuarios(id)
); 