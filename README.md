# Todo List App

A straightforward todo application that works in real-time, with Laravel + Vue.js integration.

## What it does

- Create, edit, delete todos (the basics)
- User accounts with JWT authentication
- Real-time updates when someone adds a task (uses WebSockets)
- Profile images
- Works offline for development (no external dependencies)

## Tech used

- **Backend**: Laravel 10, MySQL, JWT authentication
- **Frontend**: Vue.js 3, Pinia for state management
- **Real-time**: Soketi (local WebSocket server, Pusher-compatible)
- **Build**: Vite for fast development

## Getting it running

### What you need first

- PHP 8.1+ (with the usual extensions)
- Composer
- Node.js 16+
- MySQL or MariaDB
- Git

### Installation

```bash
# Get the code
git clone <your-repo-url>
cd todo-app

# Backend setup
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

# Frontend setup
npm install
npm install -g @soketi/soketi
```

### Database setup

Create a database called `todo_list`:

```bash
# If using MySQL
mysql -u root -p -e "CREATE DATABASE todo_list;"

# If using MariaDB 
sudo mysql -e "CREATE DATABASE todo_list;"
```

Update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_list
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Broadcasting setup
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=app-id
PUSHER_APP_KEY=app-key
PUSHER_APP_SECRET=app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

Run the database migrations:

```bash
php artisan migrate
```

## Running the app

You need three terminals open:

**Terminal 1 - WebSocket server:**
```bash
php artisan websockets:serve
```

**Terminal 2 - Laravel:**
```bash
php artisan serve
```

**Terminal 3 - Frontend:**
```bash
npm run dev
```

Open `http://127.0.0.1:8000` and you're good to go.

## API endpoints

### Authentication
```
POST /api/auth/register - Create account
POST /api/auth/login    - Sign in
```

### Tasks (requires login)
```
GET    /api/tasks     - Get your tasks
POST   /api/tasks     - Create new task
PUT    /api/tasks/{id} - Update task
DELETE /api/tasks/{id} - Delete task
```

All protected routes need the `Authorization: Bearer {token}` header.

## How the real-time stuff works

When you create a task, here's what happens:

1. Frontend sends POST to `/api/tasks`
2. Laravel saves it to database
3. Laravel fires a `TaskCreated` event
4. Event gets broadcast to the WebSocket server
5. WebSocket server pushes it to all connected clients
6. Other browser tabs update instantly

The WebSocket server (Soketi) runs locally, so no external services needed for development.

## Project structure

```
app/
├── Events/TaskCreated.php          # Real-time event
├── Http/Controllers/
│   ├── AuthController.php          # Login/register
│   └── TaskController.php          # CRUD for tasks
└── Models/
    ├── User.php
    └── Task.php

resources/js/
├── App.vue                         # Main component
├── stores/
│   ├── auth.js                     # User state
│   └── tasks.js                    # Tasks state
└── bootstrap.js                    # WebSocket setup

config/
├── broadcasting.php                # WebSocket config
└── database.php                    # DB settings
```