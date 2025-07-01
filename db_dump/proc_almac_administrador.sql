USE hospital;
DELIMITER $$

DROP PROCEDURE IF EXISTS buscarAdministrador$$
CREATE PROCEDURE buscarAdministrador (_id INT, _identificacion VARCHAR(20))
BEGIN
    SELECT * FROM administrador WHERE id_Administrador = _id OR identificacion = _identificacion;
END$$

DROP PROCEDURE IF EXISTS filtrarAdministrador$$
CREATE PROCEDURE filtrarAdministrador (
    _parametros VARCHAR(250), -- %identificacion%&%nombre%&%apellido1%&%apellido2%&%correo%&
    _pagina SMALLINT UNSIGNED, 
    _cantRegs SMALLINT UNSIGNED)
BEGIN
    SELECT cadenaFiltro(_parametros, 'identificacion&nombre&apellido1&apellido2&correo&') INTO @filtro;
    SELECT CONCAT("SELECT * FROM administrador WHERE ", @filtro, " LIMIT ", _pagina, ", ", _cantRegs) INTO @sql;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS numRegsAdministrador$$
CREATE PROCEDURE numRegsAdministrador (
    _parametros VARCHAR(250))
BEGIN
    SELECT cadenaFiltro(_parametros, 'identificacion&nombre&apellido1&apellido2&correo&') INTO @filtro;
    SELECT CONCAT("SELECT COUNT(id_Administrador) FROM administrador WHERE ", @filtro) INTO @sql;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP FUNCTION IF EXISTS nuevoAdministrador$$
CREATE FUNCTION nuevoAdministrador (
    _identificacion VARCHAR(20),
    _nombre VARCHAR(30),
    _apellido1 VARCHAR(15),
    _apellido2 VARCHAR(15),
    _telefono VARCHAR(9),
    _celular VARCHAR(9),
    _direccion VARCHAR(255),
    _correo VARCHAR(100)
) RETURNS INT(1)
DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE _cant INT;
    SELECT COUNT(id_Administrador) INTO _cant FROM administrador WHERE identificacion = _identificacion OR correo = _correo;
    IF _cant < 1 THEN
        INSERT INTO administrador(identificacion, nombre, apellido1, apellido2, telefono, celular, direccion, correo)
        VALUES (_identificacion, _nombre, _apellido1, _apellido2, _telefono, _celular, _direccion, _correo);
    END IF;
    RETURN _cant;
END$$

DROP FUNCTION IF EXISTS editarAdministrador$$
CREATE FUNCTION editarAdministrador (
    _id INT,
    _identificacion VARCHAR(20),
    _nombre VARCHAR(30),
    _apellido1 VARCHAR(15),
    _apellido2 VARCHAR(15),
    _telefono VARCHAR(9),
    _celular VARCHAR(9),
    _direccion VARCHAR(255),
    _correo VARCHAR(100)
) RETURNS INT(1)
DETERMINISTIC
MODIFIES SQL DATA
BEGIN

    DECLARE no_encontrado INT DEFAULT 0;
    IF NOT EXISTS(SELECT identificacion FROM administrador WHERE identificacion = _id) THEN
        SET no_encontrado = 1;
    ELSE
        UPDATE administrador SET
            identificacion = _identificacion,
            nombre = _nombre,
            apellido1 = _apellido1,
            apellido2 = _apellido2,
            telefono = _telefono,
            celular = _celular,
            direccion = _direccion,
            correo = _correo
        WHERE identificacion = _id;
    END IF;
    RETURN no_encontrado;
END$$

DROP FUNCTION IF EXISTS eliminarAdministrador$$
CREATE FUNCTION eliminarAdministrador (_id INT) RETURNS INT(1)
DETERMINISTIC
MODIFIES SQL DATA
BEGIN

    DECLARE _cant INT;
    DECLARE _resp INT;
    SET _resp = 0;
    SELECT COUNT(identificacion) INTO _cant FROM administrador WHERE identificacion = _id;
    IF _cant > 0 THEN
        SET _resp = 1;
        DELETE FROM administrador WHERE identificacion= _id;
    END IF;
    RETURN _resp;
END$$

DELIMITER ;

-- SELECT nuevoAdministrador(
--     '12345678',         -- identificacion
--     'Juan',             -- nombre
--     'Pérez',            -- apellido1
--     'Gómez',            -- apellido2
--     '22223333',         -- telefono
--     '88889999',         -- celular
--     'Calle 1',          -- direccion
--     'juan@correo.com'   -- correo
-- ) AS resultado;

-- SELECT editarAdministrador(
--     1,                  -- id (id_Administrador)
--     '12345678',         -- identificacion
--     'Juan Carlos',      -- nombre (nuevo valor)
--     'Pérez',            -- apellido1
--     'Gómez',            -- apellido2
--     '22223333',         -- telefono
--     '88889999',         -- celular
--     'Calle 2',          -- direccion (nuevo valor)
--     'juan@correo.com'   -- correo
-- ) AS resultado;

-- SELECT eliminarAdministrador(1) AS resultado;