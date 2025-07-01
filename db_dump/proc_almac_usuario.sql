USE hospital;
DELIMITER $$

DROP PROCEDURE IF EXISTS buscarUsuario$$
CREATE PROCEDURE buscarUsuario (_id INT, _identificacion VARCHAR(50))
BEGIN
    SELECT * FROM usuario WHERE id_Usuario = _id OR identificacion = _identificacion;
END$$



DROP FUNCTION IF EXISTS nuevoUsuario$$
CREATE FUNCTION nuevoUsuario (
    _identificacion VARCHAR(50),
    _correo VARCHAR(100),
    
    _rol VARCHAR(20),
    _passw VARCHAR(255)
) RETURNS INT(1)
DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE _cant INT;
    SELECT COUNT(id_Usuario) INTO _cant FROM usuario WHERE identificacion = _identificacion OR correo = _correo;
    IF _cant < 1 THEN
        INSERT INTO usuario(identificacion, correo,  rol, passw)
        VALUES (_identificacion, _correo,  _rol, _passw);
    END IF;
    RETURN _cant;
END$$


DROP FUNCTION IF EXISTS eliminarUsuario$$
CREATE FUNCTION eliminarUsuario (_id INT) RETURNS INT(1)
DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE _cant INT;
    DECLARE _resp INT;
    SET _resp = 0;
    SELECT COUNT(id_Usuario) INTO _cant FROM usuario WHERE id_Usuario = _id;
    IF _cant > 0 THEN
        SET _resp = 1;
        DELETE FROM usuario WHERE id_Usuario = _id;
    END IF;
    RETURN _resp;
END$$


DROP PROCEDURE IF EXISTS rolUsuario$$
CREATE PROCEDURE rolUsuario (
    _identificacion varchar(15), 
    _rol int
    ) 
begin
    update usuario set rol = _rol where identificacion = _identificacion;
end$$

DROP PROCEDURE IF EXISTS passwUsuario$$
CREATE PROCEDURE passwUsuario (
    _identificacion varchar(15), 
    _passw Varchar(255)
    )
begin
    update usuario set passw = _passw where identificacion= _identificacion;
end$$

DELIMITER ;

-- SELECT nuevoUsuario(
--     '66666666',           -- identificacion
--     'nuevo@correo.com',   -- correo
--     'claveNueva',         -- contrasena
--     'admin',              -- rol
--     NULL                  -- passw
-- ) AS resultado;

-- SELECT eliminarUsuario(6) AS resultado;