use hospital;
DELIMITER $$
-- Verifica si existe un usuario con el idUsuario (o correo) y el token de referencia (tkRef) dado.
CREATE DEFINER=`root`@`localhost` PROCEDURE `verificarTokenR` (IN `_identificacion` VARCHAR(100), IN `_tkRef` VARCHAR(255))   
begin
    select rol from usuario where (identificacion = _identificacion OR correo = _identificacion) and tkRef = _tkRef;
end$$



-- Modifica el token de referencia (tkRef) de un usuario (por idUsuario o correo).
-- Si el token no es vacío, también actualiza el campo ultimoAcceso a la fecha y hora actual.
DROP FUNCTION IF EXISTS modificarToken$$
CREATE DEFINER=`root`@`localhost` FUNCTION `modificarToken` (`_identificacion` VARCHAR(100), `_tkRef` VARCHAR(255)) 
    RETURNS INT DETERMINISTIC READS SQL DATA begin
    declare _cant int;
    select count(identificacion) into _cant from usuario where identificacion = _identificacion OR correo = _identificacion;
    if _cant > 0 then
        update usuario set
                tkRef = _tkRef -- si viene vacio se coloca vacio 
                where identificacion = _identificacion   OR correo = _identificacion;
        if _tkRef <> "" then
            update usuario set
                ultimo_acceso = now()
                where identificacion = _identificacion OR correo = _identificacion;
        end if;
    end if;
    return _cant;
end$$
DELIMITER ;