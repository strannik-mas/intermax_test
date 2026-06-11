# Customer Support Testing Task

**Requirements:** Docker and Docker Compose

## Installation

1. Clone the repository
```bash
git clone git@github.com:strannik-mas/test-smarty-blog.git
cd test-smarty
```
2. Build and run docker containers
2. Start Docker containers:
```bash
docker compose up -d
```
3. Install dependencies:
```bash
docker compose exec app composer install
```
4. Create log and temp directories:
```bash
mkdir -p log temp && chmod 777 log temp
```
5. Run migrations:
```bash
docker compose exec app php bin/migrate.php
```
6. Seed the database:
```bash
docker compose exec app php bin/seed.php
```
7. Open http://localhost:8088

## Architecture Overview

The application follows a layered architecture:

- **Presenters** — handle HTTP requests and responses
- **Services** — contain business logic
- **Repositories** — handle database access
- **Database** — MySQL/MariaDB via Nette Database Explorer

### Stack
- PHP 8.2
- Nette Framework
- MySQL/MariaDB
- Vanilla JavaScript (fetch API)
- Bootstrap 5

### Directory Structure
app/
├── Core/           # Router
├── Presentation/   # Presenters and Latte templates
├── Repository/     # Database access layer
├── Service/        # Business logic layer
database/
├── Migrations/     # SQL migration files
├── Seeders/        # PHP seeders using Faker
bin/
├── migrate.php     # Run migrations
└── seed.php        # Seed database

## API

### Search Clients
GET /api/clients

Parameters:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| name | string | — | Filter by name (partial match) |
| email | string | — | Filter by email (partial match) |
| isActive | 0/1 | — | Filter by active status |
| sortBy | string | created_at | Sort field (name, email, created_at) |
| sortOrder | ASC/DESC | DESC | Sort direction |

Response:
```json
[
  {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "is_active": true,
    "created_at": "2024-01-01 12:00:00"
  }
]
```

## Assumptions

- No authentication required — the system is used internally by trusted operators
- Comments are anonymous — no operator identity is tracked
- Activity types are predefined (login, purchase, support_ticket, profile_update, password_change)

## Known Limitations

- No authentication or authorization
- Comments have no author field — in a real system `operator_id` and a `users` table would be added
- N+1 query issue on activity comments — mitigated by batching comments per page via `WHERE IN`
- CSRF protection is implemented only for the comment form

## Potential Improvements

- Add operator authentication and authorization
- Replace offset pagination with keyset pagination for better performance at scale
- Add operator field to comments for accountability
- Add unit and integration tests
- Add input validation on the frontend
- Consider adding an activity type filter on the client detail page