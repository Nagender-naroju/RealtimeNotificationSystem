# Real-Time Notification System

A Laravel-based real-time messaging application that demonstrates event-driven architecture using WebSockets, Queue processing, and Broadcasting capabilities.

## Overview

This project implements a real-time notification system where users can send messages that are instantly broadcast to all connected clients. Every message is processed through a background queue before being broadcast, simulating real-world systems like chat applications, order status updates, and event-driven platforms.

## Features

- **REST API** for message creation
- **Queue-based processing** for background message handling
- **Real-time broadcasting** via WebSockets
- **Database persistence** for message storage
- **Frontend interface** for sending and receiving messages

## System Architecture

```
User → API Endpoint → Database → Queue Job → Broadcast → WebSocket → All Clients
```

1. User sends a message via POST request
2. Message is stored in the database
3. Message is dispatched to a queue for processing
4. Queue job processes the message
5. Processed message is broadcast via WebSockets
6. All connected clients receive the message instantly

## Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM
- Redis (recommended) or another queue driver
- MySQL/PostgreSQL or any Laravel-supported database

## Installation

### 1. Clone the Repository

```bash
git clone <your-repository-url>
cd real-time-notification-system
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment Variables

Edit your `.env` file with the following configurations:

#### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=realtime_notifications
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### Queue Configuration (Redis recommended)
```env
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Broadcasting Configuration

**Option 1: Laravel WebSockets**
```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1
```

**Option 2: Pusher**
```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

### 5. Database Setup

```bash
# Run migrations
php artisan migrate
```

### 6. Install WebSocket Server (if using Laravel WebSockets)

```bash
# Install Laravel WebSockets package
composer require beyondcode/laravel-websockets

# Publish configuration
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"

php artisan migrate

php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
```

## Running the Application

You'll need to run multiple processes:

### Terminal 1: Application Server
```bash
php artisan serve
```

### Terminal 2: Queue Worker
```bash
php artisan queue:work
```

### Terminal 3: WebSocket Server (if using Laravel WebSockets)
```bash
php artisan websockets:serve
```

### Terminal 4: Frontend Assets (optional, for development)
```bash
npm run dev
```

## API Documentation

### Create Message

**Endpoint:** `POST /api/messages`

**Request Body:**
```json
{
    "sender_id": 1,
    "message": "Hello, World!"
}
```

**Response:**
```json
{
    "success": true,
    "message_id": 123,
    "message": "Message queued for processing"
}
```

**Status Codes:**
- `200` - Success
- `422` - Validation Error
- `500` - Server Error

## Database Schema

### Messages Table

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| sender_id | bigint | User ID of the sender |
| message | text | Message content |
| processed_at | timestamp | When the message was processed |
| created_at | timestamp | When the message was created |
| updated_at | timestamp | When the message was updated |

## WebSocket Events

### Channel: `messages.channel`

**Event:** `message.received`

**Payload:**
```json
{
    "id": 123,
    "sender_id": 1,
    "message": "Hello, World!",
    "processed_at": "2024-12-10 10:30:00",
    "created_at": "2024-12-10 10:29:55"
}
```

## Frontend Usage

The frontend is accessible at `http://localhost:8000` (or your configured URL).

### Features:
- Real-time message display
- Send message form
- Automatic WebSocket connection
- Live message updates

### Testing WebSocket Connection

Open your browser's developer console to see WebSocket connection status and incoming messages.

## Queue Drivers

This project supports multiple queue drivers:

### Redis (Recommended)
```env
QUEUE_CONNECTION=redis
```

### Database
```env
QUEUE_CONNECTION=database
```

Then run:
```bash
php artisan queue:table
php artisan migrate
```

### Other Drivers
- **Beanstalkd**
- **Amazon SQS**
- **Sync** (for testing only)

## Testing

### Manual Testing

1. Open the application in multiple browser tabs
2. Send a message from one tab
3. Verify the message appears in all tabs instantly

### API Testing with cURL

```bash
curl -X POST http://localhost:8000/api/messages \
  -H "Content-Type: application/json" \
  -d '{
    "sender_id": 1,
    "message": "Test message"
  }'
```

## Git Workflow

This project follows a feature branch workflow:

### Branch Naming Convention
- `feature/message-api` - Message API implementation
- `feature/queue-processing` - Queue job implementation
- `feature/websocket-broadcast` - WebSocket broadcasting
- `feature/frontend` - Frontend interface

### Commit Message Convention
```
feat: Add message API endpoint
fix: Resolve queue processing issue
docs: Update README with setup instructions
refactor: Improve message processing logic
```

## Project Structure

```
├── app/
│   ├── Events/
│   │   └── MessageReceived.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── MessageController.php
│   ├── Jobs/
│   │   └── ProcessMessage.php
│   └── Models/
│       └── Message.php
├── database/
│   └── migrations/
│       └── xxxx_create_messages_table.php
├── resources/
│   └── views/
│       └── messages.blade.php
├── routes/
│   ├── api.php
│   └── channels.php
└── README.md
```

## Troubleshooting

### Queue not processing
```bash
# Check queue status
php artisan queue:work --once

# Clear failed jobs
php artisan queue:flush

# Restart queue worker
php artisan queue:restart
```

### WebSocket connection issues
- Verify WebSocket server is running
- Check firewall settings for port 6001
- Verify PUSHER credentials in `.env`
- Check browser console for connection errors

### Database connection issues
- Verify database credentials in `.env`
- Ensure database exists
- Check database server is running

## Performance Considerations

- Use Redis for queue and cache drivers for better performance
- Consider using Laravel Horizon for queue monitoring
- Implement rate limiting on the API endpoint
- Use database indexing for better query performance
- Consider implementing message pagination for large datasets

## Security Considerations

- Implement authentication for API endpoints
- Validate and sanitize all user inputs
- Use CSRF protection for web forms
- Implement rate limiting to prevent abuse
- Use environment variables for sensitive credentials
- Enable HTTPS in production

## Deployment

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper database credentials
- [ ] Set up Redis server
- [ ] Configure WebSocket server with SSL
- [ ] Set up queue worker as a service
- [ ] Configure proper logging
- [ ] Set up monitoring and alerts
- [ ] Implement backup strategy

### Queue Worker as Service (Supervisor)

Example supervisor configuration:

```ini
[program:realtime-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/application/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/application/storage/logs/worker.log
```

## Contributing

1. Create a feature branch from `main`
2. Make your changes with clear, descriptive commits
3. Write tests for new features
4. Update documentation as needed
5. Submit a pull request

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions:
- Create an issue in the repository
- Check existing documentation
- Review Laravel documentation at [laravel.com/docs](https://laravel.com/docs)

## Acknowledgments

- Built with [Laravel](https://laravel.com)
- WebSocket functionality powered by [Laravel WebSockets](https://beyondco.de/docs/laravel-websockets) or [Pusher](https://pusher.com)
- Queue processing using Laravel Queues

---

**Version:** 1.0.0  
**Last Updated:** December 2024