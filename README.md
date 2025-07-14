## Running This Project with Docker

This project provides a complete Docker-based development and production environment. The included Dockerfiles and `docker-compose.yaml` set up all required services and dependencies for local development and testing.

### Requirements
- Docker and Docker Compose installed on your system
- PHP 8.2 (as specified in the Dockerfile)
- MySQL, Redis, and RabbitMQ services are included and configured via Docker Compose

### Environment Variables
- Copy `.env.example` to `.env` and adjust as needed for your environment.
- The `docker-compose.yaml` references the `.env` file for configuration (uncomment the `env_file` line if you wish to use it).
- MySQL service uses the following default credentials (override in your `.env` if needed):
  - `MYSQL_DATABASE=laravel`
  - `MYSQL_USER=laravel`
  - `MYSQL_PASSWORD=secret`
  - `MYSQL_ROOT_PASSWORD=rootsecret`

### Build and Run Instructions
1. **Build and start all services:**
   ```bash
   docker compose up --build
   ```
   This will build the PHP application image and start the following services:
   - `php-app` (PHP 8.2 FPM, Composer dependencies installed)
   - `mysql-db` (MySQL database)
   - `redis-cache` (Redis cache)
   - `rabbitmq-broker` (RabbitMQ message broker)

2. **Accessing Services:**
   - PHP-FPM: exposed internally on port `9000` (not mapped to host by default)
   - MySQL: `localhost:3306`
   - Redis: `localhost:6379`
   - RabbitMQ: `localhost:5672` (AMQP), `localhost:15672` (Management UI)

3. **Artisan Commands:**
   To run artisan or composer commands inside the container:
   ```bash
   docker compose exec php-app php artisan migrate
   docker compose exec php-app composer install
   ```

### Special Configuration
- The Docker setup uses multi-stage builds for optimized production images.
- Application files are owned by a non-root user (`appuser`) for security.
- Storage and cache directories are writable by the application.
- MySQL data is persisted in a Docker volume (`mysql-data`).

For further customization, review the `docker/` directory and the `docker-compose.yaml` file.
