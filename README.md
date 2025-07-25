# TaskAPI

## Project Setup

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL or another supported database
- Node.js & npm (for frontend assets, if needed)
- [Optional] WAMP/XAMPP/Laragon for local development

### Installation
1. **Clone the repository:**
   ```bash
   git clone <repo-url>
   cd TaskAPI
   ```
2. **Install PHP dependencies:**
   ```bash
   composer install
   ```
3. **Copy the example environment file and configure:**
   ```bash
   cp .env.example .env
   # Edit .env to set your database credentials and other settings
   ```
4. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

5. **(Optional) Install Node dependencies and build assets:**
   ```bash
   npm install
   npm run build
   ```

## Database Setup

### Run Migrations
```bash
php artisan migrate
```

## Running the Application

Start the local development server:
```bash
php artisan serve
```
The API will be available at `http://localhost:8000` by default.

## API Authentication
This project uses [Laravel Sanctum](https://laravel.com/docs/sanctum) for API authentication. Make sure to login and use the provided token for authenticated routes.

## Testing the API with the Pre-built Frontend

1. **Ensure the backend (API) is running** as described above.
2. **Navigate to the frontend repository and clone the code using git clone**.
```bash
   git clone https://github.com/CodeArchitectSam/TaskAPI-frontend.git
   ```
3. **Install frontend dependencies and run the frontend:**
   ```bash
   npm install
   npm start
   ```
   This will start the frontend on a local port (commonly `http://localhost:3000` or similar).
4. **Access the frontend in your browser** and interact with the UI. The frontend is pre-configured to communicate with the API endpoints.

## API Endpoints

| Method     | URI                          | Description                                      |
|------------|------------------------------|--------------------------------------------------|
| POST       | /api/login                   | Authenticate user and return access token         |
| POST       | /api/logout                  | Logout the authenticated user                    |
| POST       | /api/register                | Register a new user                              |
| GET        | /api/tasks                   | Get a list of all tasks (with filters, pagination)|
| POST       | /api/tasks                   | Create a new task                                |
| GET        | /api/tasks/{task_id}/comments| Get all comments for a specific task             |
| POST       | /api/tasks/{task_id}/comments| Add a new comment to a specific task             |
| GET        | /api/tasks/{task}            | Get details of a specific task                   |
| PUT/PATCH  | /api/tasks/{task}            | Update a specific task                           |
| DELETE     | /api/tasks/{task}            | Delete a specific task                           |

## Notes
- Ensure your `.env` file is properly configured for your local database and mail settings.
- For API testing, you can also use Postman or similar tools. Remember to set the `Accept: application/json` header for proper responses.

## Screenshots
- Check the public/screenshots folder