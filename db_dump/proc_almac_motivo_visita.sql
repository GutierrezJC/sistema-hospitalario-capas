USE hospital;
DELIMITER $$

DROP PROCEDURE IF EXISTS buscarMotivoVisita$$
CREATE PROCEDURE buscarMotivoVisita (_id INT)
BEGIN
    SELECT * FROM motivo_visita WHERE id_motivo = _id;
END$$

DROP PROCEDURE IF EXISTS filtrarMotivoVisita$$
CREATE PROCEDURE filtrarMotivoVisita (
    _parametros VARCHAR(250), -- %descripcion%&
    _pagina SMALLINT UNSIGNED, 
    _cantRegs SMALLINT UNSIGNED)
BEGIN
    SELECT cadenaFiltro(_parametros, 'descripcion&') INTO @filtro;
    SELECT CONCAT("SELECT * FROM motivo_visita WHERE ", @filtro, " LIMIT ", _pagina, ", ", _cantRegs) INTO @sql;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS numRegsMotivoVisita$$
CREATE PROCEDURE numRegsMotivoVisita (
    _parametros VARCHAR(250))
BEGIN
    SELECT cadenaFiltro(_parametros, 'descripcion&') INTO @filtro;
    SELECT CONCAT("SELECT COUNT(id_motivo) FROM motivo_visita WHERE ", @filtro) INTO @sql;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP FUNCTION IF EXISTS nuevoMotivoVisita$$
CREATE FUNCTION nuevoMotivoVisita (
    _descripcion VARCHAR(255)
) RETURNS INT(1)
DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE _cant INT;
    SELECT COUNT(id_motivo) INTO _cant FROM motivo_visita WHERE descripcion = _descripcion;
    IF _cant < 1 THEN
        INSERT INTO motivo_visita(descripcion)
        VALUES (_descripcion);
    END IF;
    RETURN _cant;
END$$

DROP FUNCTION IF EXISTS editarMotivoVisita$$
CREATE FUNCTION editarMotivoVisita (
    _id INT,
    _descripcion VARCHAR(255)
) RETURNS INT(1)
DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE no_encontrado INT DEFAULT 0;
    IF NOT EXISTS(SELECT id_motivo FROM motivo_visita WHERE id_motivo = _id) THEN
        SET no_encontrado = 1;
    ELSE
        UPDATE motivo_visita SET
            descripcion = _descripcion
        WHERE id_motivo = _id;
    END IF;
    RETURN no_encontrado;
END$$

DROP FUNCTION IF EXISTS eliminarMotivoVisita$$
CREATE FUNCTION eliminarMotivoVisita (_id INT) RETURNS INT(1)
DETERMINISTIC
MODIFIES SQL DATA
BEGIN
    DECLARE _cant INT;
    DECLARE _resp INT;
    SET _resp = 0;
    SELECT COUNT(id_motivo) INTO _cant FROM motivo_visita WHERE id_motivo = _id;
    IF _cant > 0 THEN
        SET _resp = 1;
        DELETE FROM motivo_visita WHERE id_motivo = _id;
    END IF;
    RETURN _resp;
END$$

DELIMITER ;


-- INSERT INTO motivo_visita (descripcion) VALUES
-- ('Entrega incapacidad'),
-- ('Revisión tratamiento'),
-- ('Autorización procedimiento'),
-- ('Queja médica'),
-- ('Evaluación laboral'),
-- ('Coordinación traslado'),
-- ('Supervisión clínica');

-- CALL filtrarMotivoVisita('%Entrega%&', 0, 10);

-- SELECT nuevoMotivoVisita('Entrega incapacidad') AS resultado;
-- SELECT editarMotivoVisita(1, 'Entrega de incapacidad') AS resultado;
-- SELECT eliminarMotivoVisita(1) AS resultado;