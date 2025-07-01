
USE hospital;
DELIMITER $$

DROP FUNCTION IF EXISTS cadenaFiltro$$
CREATE FUNCTION cadenaFiltro(_parametros VARCHAR(250), _campos VARCHAR(250))
RETURNS VARCHAR(1000)
DETERMINISTIC
NO SQL
BEGIN
    DECLARE _filtro VARCHAR(1000) DEFAULT '';
    DECLARE _valor VARCHAR(50);
    DECLARE _campo VARCHAR(50);
    WHILE (LOCATE('&', _parametros) > 0) DO
        SET _valor = SUBSTRING_INDEX(_parametros, '&', 1);
        SET _parametros = SUBSTR(_parametros, LOCATE('&', _parametros) + 1);
        SET _campo = SUBSTRING_INDEX(_campos, '&', 1);
        SET _campos = SUBSTR(_campos, LOCATE('&', _campos) + 1);
        IF _valor IS NOT NULL AND _valor <> '' THEN
            SET _filtro = CONCAT(_filtro, " `", _campo, "` LIKE '%", _valor, "%' and");
        END IF;
    END WHILE;
    SET _filtro = TRIM(TRAILING 'and' FROM _filtro);
    IF _filtro = '' THEN
        SET _filtro = '1=1';
    END IF;
    RETURN _filtro;
END$$

DELIMITER ;


-- SEGUNDO PARA FECHA 