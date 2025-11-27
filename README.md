# Cotizador de Paqueterías

## Descripción
Este proyecto implementa un endpoint para cotizar envíos de paqueterías simulando dos proveedores externos.  
Se incluyen logs, persistencia de carriers en base de datos MySQL y validación de los datos de entrada.

El proyecto está desarrollado con **Symfony 6**, PHP 8.2 y utiliza Doctrine ORM para manejar la base de datos.

---

## Requisitos
- PHP >= 8.2
- Composer
- MySQL
- Symfony CLI (opcional para servidor local)
- Extensiones de PHP necesarias: `pdo_mysql`, `curl`, `json`

---

## Instalación

1. Clonar el repositorio:

```bash
git clone https://github.com/tu-usuario/cotizador.git
cd cotizador

2. Instalar dependencias:

```bash 
composer install

3. Configurar la base de datos en .env o .env.dev:
DATABASE_URL="mysql://root:@127.0.0.1:3306/cotizador?charset=utf8mb4"

4. Crear la base de datos y ejecutar migraciones:
```bash 
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

5. Cargar datos de prueba (fixtures):
```bash 
php bin/console doctrine:fixtures:load

6. Iniciar el servidor de desarrollo:
```bash 
 php -S localhost:8000 -t public


## Estructura del Proyecto
/src
 ├── Controller        # Controladores del API
 ├── Service           # Lógica de negocio (cotización, integración carriers)      # Repositorios Doctrine
 ├── Entity            # Entidades de base de datos
 └── DataFixtures      # Datos de prueba
/config
/public
/migrations


## Flujo Interno

## Flujo Interno

1. El usuario envía una petición con origen, destino y peso.
2. El sistema valida los datos.
3. Se consultan dos carriers simulados.
4. Se calcula el precio final.
5. Se retorna el carrier más económico.
6. Se registra un log del proceso.
