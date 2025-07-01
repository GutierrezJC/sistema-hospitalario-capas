# Comandos Docker para gestión del proyecto

# Construcción y despliegue
docker-compose build --no-cache     # Construir todas las imágenes
docker-compose up -d                # Levantar todos los servicios
docker-compose down                 # Detener todos los servicios

# Monitoreo
docker-compose ps                   # Ver estado de contenedores
docker-compose logs -f              # Ver logs en tiempo real
docker-compose logs [servicio]      # Ver logs de un servicio específico

# Gestión individual de servicios
docker-compose up -d webpresentacion  # Solo levantar presentación
docker-compose restart webnegocio     # Reiniciar capa de negocio
docker-compose stop db               # Detener base de datos

# Limpieza
docker-compose down --volumes       # Eliminar contenedores y volúmenes
docker system prune -a              # Limpiar imágenes no utilizadas

# Acceso a contenedores
docker exec -it web_presentacion sh    # Acceder al contenedor de presentación
docker exec -it web_negocio bash       # Acceder al contenedor de negocio
docker exec -it base_datos mysql -u root -p  # Acceder a MySQL

# Respaldo y restauración
docker exec base_datos mysqldump -u root -p hospital > backup.sql
docker exec -i base_datos mysql -u root -p hospital < backup.sql
