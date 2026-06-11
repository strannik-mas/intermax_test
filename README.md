# Customer Support Testing Task

**Requirements:** Docker and Docker Compose

## Installation

1. Clone the repository
```bash
git clone git@github.com:strannik-mas/intermax_test.git
cd intermax_test
```
2. Build and run docker containers
```bash
docker compose up -d --build
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
- **Database** — MySQL via Nette Database Explorer

### Stack
- PHP 8.2
- Nette Framework
- MySQL
- Vanilla JavaScript (fetch API)
- Bootstrap 5

### Project Structure
- `app/Presentation` — presenters and latte templates
- `app/Repository` — database queries
- `app/Service` — business logic (thin layer, but leaves room to grow)
- `database/Migrations` — SQL files, run in order via `bin/migrate.php`
- `database/Seeders` — test data generation with Faker

## API

### GET /api/clients

Returns a list of clients as JSON. All parameters are optional.

- `name` — partial name match
- `email` — partial email match
- `isActive` — pass `1` for active, `0` for inactive, omit for all
- `sortBy` — one of `name`, `email`, `created_at` (default)
- `sortOrder` — `ASC` or `DESC` (default)

Example: `/api/clients?name=john&isActive=1&sortBy=name&sortOrder=ASC`

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

### POST /client/detail/{id}?do=addComment

Adds a comment to a client activity. Used internally by the frontend via AJAX.

Parameters (form data):
- `activityId` — ID of the activity
- `comment` — comment text
- `_token` — CSRF token (taken from the hidden field in the form)

Response:
```json
{"success": true, "comment": "text", "created_at": "2024-01-01 12:00:00"}
```

Comments are added without page reload using the fetch API. The CSRF token is included in every request via a hidden form field.

## Assumptions

- Authentication is not required — the system is used internally by trusted operators
- Comments are anonymous — operator identities are not tracked
- Action types are predefined (login, purchase, support request, profile update, password change)

## Known Limitations

- No authentication or authorization
- Comments lack an author field — in a real system, operator_id fields and a user table would be added
- The N+1 request problem for comments per activity is solved by batch processing comments on each page using WHERE IN
- CSRF protection is implemented only for the comment form

## Possible Improvements

- Add operator authentication and authorization
- Add an operator field in comments to ensure accountability
- Switch activity pagination from offset to keyset to improve performance on large datasets
- Add unit and integration tests
- Add user-friendly data validation feedback on the frontend (highlighting empty fields, displaying error messages in (line)
- Add a filter by activity type on the client detail page

## Scalability Considerations

Database indexes are established on frequently accessed columns: `client_activities(client_id, created_at, id)` for queries with activity pagination, `clients(name)`, `clients(email)`, and `clients(is_active)` for searching and filtering. The activity list is paginated to avoid loading large data sets simultaneously. Page comments are loaded in a single batch request, rather than separate requests for each activity in a loop.

## Time Spent

About 8 hours. Most of that time was spent learning Nette, which I hadn't worked with before. Setting up the Docker environment and understanding how the dependency injection container, presenters, and Nette routing interact with each other took longer than expected. The actual implementation of the features turned out to be quite simple once I got the hang of the framework.