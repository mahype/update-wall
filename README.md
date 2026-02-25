# Update Wall

Web dashboard for monitoring updates on servers and machines. Receives data from [Update Watcher](https://github.com/mahype/update-watcher) clients via a REST API and displays the status of all machines in a clear overview.

## Features

- **Dashboard** with color coding by update status (green/yellow/red)
- **Machine detail view** with checker accordion and historical reports
- **REST API** `POST /api/v1/report` with Bearer token authentication
- **Admin panel** — user, token, and machine management
- **API documentation** in the admin area (Markdown-based, with copy function)
- **Scheduled tasks** — stale detection and automatic report cleanup

## Tech Stack

| Component | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Database | SQLite |
| Frontend | Blade + Tailwind CSS 3 + Alpine.js 3 |
| Auth | Laravel Breeze (Blade stack) |
| Markdown | league/commonmark + @tailwindcss/typography |

## Requirements

- PHP >= 8.2 with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- Composer
- Node.js >= 18 + npm

## Installation

```bash
# Clone the repository
git clone https://github.com/mahype/update-wall.git
cd update-wall

# Install PHP dependencies
composer install

# Install Node dependencies and build assets
npm install && npm run build

# Create environment file
cp .env.example .env
# Adjust APP_URL in .env
```

Then start the dev server:

```bash
php artisan serve
```

Open `/install` in the browser, fill out the form, and you're done.

## Demo Data (optional)

Complete `/install` first, then:

```bash
php artisan db:seed
```

Creates an admin user (`admin@example.com` / `password`), a demo API token, and 5 sample machines with various statuses.

## Development

```bash
# Start dev server
php artisan serve

# Vite dev server (hot reload)
npm run dev
```

## Production / Shared Hosting

```bash
# Build assets for production (public/build/ is committed)
npm run build
```

Adjust `.env`:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

Then open `/install` in the browser, fill out the form, and you're done.

### Set Up Cron Job

```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks:
- `machines:mark-stale` — Every 15 min: mark machines without a report for 25h as "stale"
- `reports:prune --days=90` — Daily: delete old reports (keep the latest report per machine)

## API

### Send Report

```bash
curl -X POST https://your-domain.com/api/v1/report \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR-API-TOKEN" \
  -d '{
    "hostname": "webserver-prod",
    "timestamp": "2026-02-25T10:15:00Z",
    "total_updates": 1,
    "has_security": true,
    "checkers": [{
      "name": "apt",
      "summary": "1 update available",
      "updates": [{
        "name": "libssl3",
        "current_version": "3.0.2-1",
        "new_version": "3.0.2-2",
        "type": "security",
        "priority": "critical"
      }]
    }]
  }'
```

API tokens are created in the admin panel under **API Tokens**. The full API documentation is available in the admin panel under **API Docs**.

## Artisan Commands

| Command | Description |
|---|---|
| `php artisan machines:mark-stale --hours=25` | Mark inactive machines as "stale" |
| `php artisan reports:prune --days=90` | Clean up old reports |

## License

MIT
