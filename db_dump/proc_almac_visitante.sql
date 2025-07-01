USE hospital;
DELIMITER $$

-- Buscar visitante por id o identificacion
DROP PROCEDURE IF EXISTS buscarVisitante$$
CREATE PROCEDURE buscarVisitante (_id INT, _identificacion VARCHAR(20))
BEGIN
    SELECT * FROM visitante WHERE id_Visitante = _id OR identificacion = _identificacion;
END$$

-- Filtrar visitante usando cadenaFiltro
DROP PROCEDURE IF EXISTS filtrarVisitante$$
CREATE PROCEDURE filtrarVisitante (
    _parametros VARCHAR(250), -- %identificacion%&%nombre%&%apellido1%&%apellido2%&%correo%&%sector_laboral%&
    _pagina SMALLINT UNSIGNED, 
    _cantRegs SMALLINT UNSIGNED)
BEGIN
    SELECT cadenaFiltro(_parametros, 'identificacion&nombre&apellido1&apellido2&correo&sector_laboral') INTO @filtro;
    SELECT CONCAT("SELECT * FROM visitante WHERE ", @filtro, " LIMIT ", _pagina, ", ", _cantRegs) INTO @sql;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

-- Contar visitantes usando cadenaFiltro
DROP PROCEDURE IF EXISTS numRegsVisitante$$
CREATE PROCEDURE numRegsVisitante (
    _parametros VARCHAR(250))
BEGIN
    SELECT cadenaFiltro(_parametros, 'identificacion&nombre&apellido1&apellido2&correo&sector_laboral') INTO @filtro;
    SELECT CONCAT("SELECT COUNT(id_Visitante) FROM visitante WHERE ", @filtro) INTO @sql;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

-- Insertar visitante
DROP FUNCTION IF EXISTS nuevoVisitante$$
CREATE FUNCTION nuevoVisitante (
    _identificacion VARCHAR(20),
    _nombre VARCHAR(30),
    _apellido1 VARCHAR(30),
    _apellido2 VARCHAR(30),
    _telefono VARCHAR(15),
    _correo VARCHAR(100),
    _sector_laboral VARCHAR(50)
) RETURNS INT(1)
DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE _cant INT;
    SELECT COUNT(id_Visitante) INTO _cant FROM visitante WHERE identificacion = _identificacion OR correo = _correo;
    IF _cant < 1 THEN
        INSERT INTO visitante(identificacion, nombre, apellido1, apellido2, telefono, correo, sector_laboral)
        VALUES (_identificacion, _nombre, _apellido1, _apellido2, _telefono, _correo, _sector_laboral);
    END IF;
    RETURN _cant;
END$$

-- Editar visitante
DELIMITER $$
DROP FUNCTION IF EXISTS editarVisitante$$
CREATE FUNCTION editarVisitante (
     _id INT,
    _identificacion VARCHAR(20),
    _nombre VARCHAR(30),
    _apellido1 VARCHAR(30),
    _apellido2 VARCHAR(30),
    _telefono VARCHAR(15),
    _correo VARCHAR(100),
    _sector_laboral VARCHAR(50)
) RETURNS INT(1)
DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE no_encontrado INT DEFAULT 0;
    IF NOT EXISTS(SELECT identificacion FROM visitante WHERE identificacion = _id) THEN
        SET no_encontrado = 1;
    ELSE
        UPDATE visitante SET
            identificacion = _identificacion,
            nombre = _nombre,
            apellido1 = _apellido1,
            apellido2 = _apellido2,
            telefono = _telefono,
            correo = _correo,
            sector_laboral = _sector_laboral
        WHERE identificacion = _id;
    END IF;
    RETURN no_encontrado;
END$$

-- Eliminar visitante
DROP FUNCTION IF EXISTS eliminarVisitante$$
CREATE FUNCTION eliminarVisitante (_id INT) RETURNS INT(1)
DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE _cant INT;
    DECLARE _resp INT;
    SET _resp = 0;
    SELECT COUNT(id_Visitante) INTO _cant FROM visitante WHERE identificacion= _id;
    IF _cant > 0 THEN
        SET _resp = 1;
        -- Aquí puedes agregar chequeo de dependencias si lo necesitas
        DELETE FROM visitante WHERE identificacion = _id;
    END IF;
    RETURN _resp;
END$$

DELIMITER ;


-- SELECT nuevoVisitante(
--     '77777777',          -- identificacion
--     'Carlos',            -- nombre
--     'Mora',              -- apellido1
--     'Soto',              -- apellido2
--     '22227777',          -- telefono
--     'carlos@correo.com', -- correo
--     'Educación'          -- sector_laboral
-- ) AS resultado;

-- CALL filtrarVisitante('&&Mora&&&&', 0, 10);

-- SELECT editarVisitante(
--     '77777777',          -- identificacion
--     'Carlos',            -- nombre
--     'Morales',           -- apellido1 (nuevo valor)
--     'Soto',              -- apellido2
--     '22227777',          -- telefono
--     'carlos@correo.com', -- correo
--     'Educación'          -- sector_laboral
-- ) AS resultado;

-- SELECT eliminarVisitante(1) AS resultado;