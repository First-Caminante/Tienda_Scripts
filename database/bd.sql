CREATE DATABASE TiendaScripts;

use TiendaScripts;


CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'desarrollador', 'admin') NOT NULL DEFAULT 'cliente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    estado ENUM('pendiente', 'en proceso', 'completado', 'rechazado') NOT NULL DEFAULT 'pendiente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
CREATE TABLE respuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitud_id INT NOT NULL,
    desarrollador_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    archivo_script VARCHAR(255),  -- Ruta del script en el servidor
    fecha_respuesta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes(id) ON DELETE CASCADE,
    FOREIGN KEY (desarrollador_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    solicitud_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'pagado', 'fallido') NOT NULL DEFAULT 'pendiente',
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes(id) ON DELETE CASCADE
);





DELIMITER //
CREATE PROCEDURE CrearUsuario(
    IN p_nombre VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_password_hash VARCHAR(255),
    IN p_rol ENUM('cliente', 'desarrollador', 'admin')
)
BEGIN
    INSERT INTO usuarios(nombre, email, password_hash, rol)
    VALUES (p_nombre, p_email, p_password_hash, p_rol);
END;
//
DELIMITER ;




DELIMITER //
CREATE PROCEDURE ObtenerUsuarios()
BEGIN
    SELECT id, nombre, email, rol, fecha_registro FROM usuarios;
END;
//
DELIMITER ;





DELIMITER //
CREATE PROCEDURE ObtenerUsuarioPorID(IN p_id INT)
BEGIN
    SELECT id, nombre, email, rol, fecha_registro
    FROM usuarios
    WHERE id = p_id;
END;
//
DELIMITER ;





DELIMITER //
CREATE PROCEDURE ActualizarUsuario(
    IN p_id INT,
    IN p_nombre VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_password_hash VARCHAR(255),
    IN p_rol ENUM('cliente', 'desarrollador', 'admin')
)
BEGIN
    UPDATE usuarios
    SET nombre = p_nombre,
        email = p_email,
        password_hash = p_password_hash,
        rol = p_rol
    WHERE id = p_id;
END;
//
DELIMITER ;






DELIMITER //
CREATE PROCEDURE EliminarUsuario(IN p_id INT)
BEGIN
    DELETE FROM usuarios WHERE id = p_id;
END;
//
DELIMITER ;




DELIMITER //
CREATE PROCEDURE LoginUsuario(
    IN p_email VARCHAR(100),
    IN p_password_hash VARCHAR(255)
)
BEGIN
    SELECT id, nombre, email, rol
    FROM usuarios
    WHERE email = p_email AND password_hash = p_password_hash;
END;
//
DELIMITER ;



DELIMITER //
CREATE PROCEDURE VerificarEmailExiste(
    IN p_email VARCHAR(100)
)
BEGIN
    SELECT COUNT(*) AS existe
    FROM usuarios
    WHERE email = p_email;
END;
//
DELIMITER ;



DELIMITER //

CREATE PROCEDURE LoginUsuario(
    IN p_email VARCHAR(100),
    IN p_password_hash VARCHAR(255)
)
BEGIN
    SELECT id, nombre, email, rol
    FROM usuarios
    WHERE email = p_email AND password_hash = p_password_hash;
END;
//

DELIMITER ;




