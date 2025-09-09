#Prompt para bot 

Eres un bot encargado de registrar turnos para clientes en una empresa utilizando la API REST documentada. Debes seguir el siguiente flujo y utilizar los endpoints correspondientes:

Reconocer o crear cliente:

Solicita el teléfono del cliente.
Busca el cliente por teléfono usando:
GET /api/buscarportelefono?telefono={telefono}
Si el cliente no existe, solicita los datos básicos y créalo usando:
POST /api/clientes
(requiere: empresa_id, nombre, apellido, email, teléfono, documento, fecha_nacimiento, observaciones, activo)
Listar servicios de la empresa:

Solicita el ID de la empresa.
Lista los servicios disponibles usando:
GET /api/empresas/{empresa_id}/servicios
Seleccionar servicio y listar turnos disponibles:

Solicita el servicio deseado.
Solicita la fecha para el turno.
Lista los turnos disponibles para ese servicio usando:
GET /api/turnosdisponiblesporservicio?servicio_id={servicio_id}&empresa_id={empresa_id}&fecha={fecha}
Seleccionar y reservar turno:

Solicita el turno (slot) que el cliente desea reservar.
Registra el turno usando:
POST /api/turnos/add
(requiere: empresa_id, cliente_id, servicio_id, recurso_id, fecha_hora_inicio, fecha_hora_fin, estado)
Confirmar o cancelar turno:

Si el cliente desea cancelar, actualiza el turno usando:
PATCH /api/turnos/{turno_id}
(requiere: empresa_id, cliente_id, servicio_id, recurso_id, fecha_hora_inicio, estado="cancelado", observaciones)
Notas:

Todos los endpoints requieren autenticación con token Bearer.
Los IDs deben ser obtenidos dinámicamente según las respuestas de la API.
Valida y muestra mensajes claros en cada paso.
Tu tarea es guiar al usuario por este flujo, realizar las llamadas a la API y mostrar los resultados o errores de forma comprensible.



# Flujo de prueba de turnos
    //estos serian los pasos a seguir para solicitar un turno
    // 1 reconocer el cliente (por telefono)
    // si no existe, crearlo solicitar datos basicos
    // si existe, obtener su id
    //1. listar servicios de una empresa que ya sabemos cual es
    //2. seleccionar un servicio
    //3. listar si turnos disponibles por servicio
    //4. seleccionar un turno
    //5confirmar un turno con un pago (esto ya lo tenemos con addTurno)

Esta sección documenta el flujo completo para testear la gestión de turnos vía API, incluyendo ejemplos de cada paso.

## 1. Reconocer o crear cliente

Buscar cliente por teléfono:

```bash
curl -X GET "http://localhost:1234/api/buscarportelefono?telefono=2942506803" -H "Authorization: Bearer <TOKEN>"
```
Si el cliente no existe, crear uno:

```bash
curl -X POST http://localhost:1234/api/clientes -H "Authorization: Bearer <TOKEN>" -H "Content-Type: application/json" -d '{
	"empresa_id": 1,
	"nombre": "javier",
	"apellido": "desmaret",
	"email": "mause.javi@gmail.com",
	"telefono": "2942506803",
	"documento": "35833716",
	"fecha_nacimiento": "2025-04-11",
	"observaciones": "",
	"activo": true
}'
```

## 2. Listar servicios de la empresa

```bash
curl -X GET http://localhost:1234/api/empresas/1/servicios -H "Authorization: Bearer <TOKEN>"
```

## 3. Listar turnos disponibles por servicio

```bash
curl -X GET "http://localhost:1234/api/turnosdisponiblesporservicio?servicio_id=3&empresa_id=1&fecha=2025-09-07" -H "Authorization: Bearer <TOKEN>"
```

## 4. Reservar un turno

```bash
curl -X POST http://localhost:1234/api/turnos/add -H "Authorization: Bearer <TOKEN>" -H "Content-Type: application/json" -d '{
	"empresa_id": 1,
	"cliente_id": 11,
	"servicio_id": 3,
	"recurso_id": 1,
	"fecha_hora_inicio": "2025-09-07T08:00:00",
	"fecha_hora_fin": "2025-09-07T09:30:00",
	"estado": "pendiente"
}'
```

## 5. Cancelar un turno

Enviar todos los datos requeridos:

```bash
curl -X PATCH http://localhost:1234/api/turnos/5 -H "Authorization: Bearer <TOKEN>" -H "Content-Type: application/json" -d '{
	"empresa_id": 1,
	"cliente_id": 11,
	"servicio_id": 3,
	"recurso_id": 1,
	"fecha_hora_inicio": "2025-09-07T08:00:00",
	"duracion_personalizada_minutos": null,
	"estado": "cancelado",
	"observaciones": "Cancelado por el cliente",
	"precio_final": null
}'
```

## Notas
- Reemplaza `<TOKEN>` por el token de autenticación válido.
- Los IDs deben ajustarse según los datos creados en tu entorno.


## Forzar actualización del repositorio local (descartar cambios locales)

Si necesitas actualizar tu repositorio local desde el remoto y descartar todos los cambios locales no confirmados, puedes usar:

```bash
git fetch --all
git reset --hard origin/main
```

Esto sobrescribirá tu rama local con la versión remota y perderás todos los cambios locales no confirmados. Úsalo solo si estás seguro de que no necesitas tus cambios locales.


# Turnero
Turnero para administrar empresas

## Endpoints API para gestión de turnos

### Listar turnos disponibles por recurso

`GET /api/turnos/disponibles-por-recurso?empresa_id=ID&fecha=YYYY-MM-DD`

**Parámetros:**
- `empresa_id` (int, requerido): ID de la empresa
- `fecha` (string, requerido): Fecha en formato `YYYY-MM-DD`

**Respuesta:**
```json
{
	"Recurso 1": {
		"slots": [
			{"servicio": "Servicio A", "inicio": "2025-09-05 08:00", "fin": "2025-09-05 08:30"},
			// ...
		],
		"cantidad_servicios_disponibles": 5
	},
	// ...
}
```

**Ejemplo:**
```bash
curl -X GET "http://localhost:8000/api/turnos/disponibles-por-recurso?empresa_id=1&fecha=2025-09-05"
```

### Crear un turno (addTurno)

`POST /api/turnos/add`

**Body:**
```json
{
	"empresa_id": 1,
	"cliente_id": 1,
	"servicio_id": 1,
	"recurso_id": 1,
	"fecha_hora_inicio": "2025-09-05 20:00"
}
```

**Respuesta:**
```json
{
	"id": 123,
	"empresa_id": 1,
	"cliente_id": 1,
	"servicio_id": 1,
	"recurso_id": 1,
	"fecha_hora_inicio": "2025-09-05 20:00",
	"fecha_hora_fin": "2025-09-05 20:30",
	"estado": "pendiente",
	// ...
}
```

**Ejemplo:**
```bash
curl -X POST "http://localhost:8000/api/turnos/add" \
	-H "Content-Type: application/json" \
	-d '{
		"empresa_id": 1,
		"cliente_id": 1,
		"servicio_id": 1,
		"recurso_id": 1,
		"fecha_hora_inicio": "2025-09-05 20:00"
	}'
```

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
