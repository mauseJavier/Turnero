## Forzar actualización del repositorio local (descartar cambios locales)

Si necesitas actualizar tu repositorio local desde el remoto y descartar todos los cambios locales no confirmados, puedes usar:

```bash
git fetch --all
git reset --hard origin/main
```

Esto sobrescribirá tu rama local con la versión remota y perderás todos los cambios locales no confirmados. Úsalo solo si estás seguro de que no necesitas tus cambios locales.

# Turnero
Turnero para administrar empresas

## Limpiar base de datos y poblarla con datos de ejemplo

Para eliminar todas las tablas, ejecutar las migraciones desde cero y poblar la base de datos con los seeders configurados, usa:

```bash
php artisan migrate:fresh --seed
```

Este comando borra todas las tablas, ejecuta todas las migraciones y luego ejecuta los seeders definidos en `DatabaseSeeder`. Es útil para reiniciar el estado de la base de datos durante el desarrollo.

## Desarrollo con Docker Compose

Para levantar el entorno de desarrollo usando Docker Compose:

1. Asegúrate de tener Docker y Docker Compose instalados.
2. Ejecuta el siguiente comando en la raíz del proyecto:

```bash
docker compose -f docker-dev.yml up -d --build
```

Esto iniciará los siguientes servicios:
- **turnero**: Contenedor con PHP y Composer para Laravel
- **db**: Base de datos MariaDB
- **phpmyadmin**: Interfaz web para administrar la base de datos (accesible en [http://localhost:8080](http://localhost:8080))

Para detener los servicios:

```bash
docker compose -f docker-dev.yml down
```

Los archivos del proyecto se montan como volumen, por lo que los cambios locales se reflejan en el contenedor.
