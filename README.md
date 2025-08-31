# Turnero
Turnero para administrar empresas

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
