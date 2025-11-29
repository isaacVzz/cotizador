# Cotizador API

API REST para obtener cotizaciones de diferentes carriers/proveedores de envío.

## Requisitos

- PHP >= 8.1
- Composer
- MySQL/MariaDB

## Instalación

1. Clonar el repositorio
2. Instalar dependencias:

```bash
composer install
```

3. Configurar variables de entorno en `.env`:

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/cotizador?charset=utf8mb4"
API_KEY=your-secret-api-key-here
```

4. Crear base de datos y ejecutar migraciones:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

## Ejecutar el servidor

```bash
php -S localhost:8000 -t public
```

## Endpoints

### POST /api/quote

Obtiene cotizaciones de todos los carriers activos.

**Autenticación**: Requiere header `X-API-Key` con la clave configurada en `API_KEY`.

**Request Body**:

```json
{
  "originZipcode": "12345",
  "destinationZipcode": "67890"
}
```

**Ejemplo con cURL**:

```bash
curl -X POST http://localhost:8000/api/quote \
  -H "Content-Type: application/json" \
  -H "X-API-Key: your-secret-api-key-here" \
  -d '{
    "originZipcode": "12345",
    "destinationZipcode": "67890"
  }'
```

**Respuesta exitosa (200)**:

```json
{
  "success": true,
  "results": [
    {
      "carrier": "Carrier Success",
      "success": true,
      "price": 120.50,
      "provider_response": {...}
    },
    {
      "carrier": "Carrier Fail",
      "success": false,
      "error": "Provider error - service unavailable",
      "provider_response": {...}
    }
  ]
}
```

**Respuesta de error (400)**:

```json
{
  "success": false,
  "error": "originZipcode and destinationZipcode are required"
}
```

**Respuesta de error (401)**:

```json
{
  "success": false,
  "error": "Authentication failed: Invalid API key"
}
```

## Ejecutar pruebas

```bash
php bin/phpunit
```

## Logs

Los logs se encuentran en `var/log/dev.log`.
