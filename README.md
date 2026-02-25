# Update Wall

Web-Dashboard zur Überwachung von Updates auf Servern und Maschinen. Empfängt Daten von [Update Watcher](https://github.com/mahype/update-watcher)-Clients über eine REST-API und zeigt den Status aller Maschinen in einer übersichtlichen Oberfläche.

## Features

- **Dashboard** mit Farbkodierung nach Update-Status (grün/gelb/rot)
- **Maschinen-Detailansicht** mit Checker-Akkordeon und historischen Reports
- **REST-API** `POST /api/v1/report` mit Bearer-Token-Authentifizierung
- **Admin-Panel** — Benutzer-, Token- und Maschinen-Verwaltung
- **API-Dokumentation** im Admin-Bereich (Markdown-basiert, mit Kopier-Funktion)
- **Scheduled Tasks** — Stale-Erkennung und automatische Report-Bereinigung

## Tech-Stack

| Komponente | Technologie |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Datenbank | SQLite |
| Frontend | Blade + Tailwind CSS 3 + Alpine.js 3 |
| Auth | Laravel Breeze (Blade-Stack) |
| Markdown | league/commonmark + @tailwindcss/typography |

## Voraussetzungen

- PHP >= 8.2 mit den Erweiterungen: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- Composer
- Node.js >= 18 + npm

## Installation

```bash
# Repository klonen
git clone https://github.com/DEIN-USER/update-wall.git
cd update-wall

# PHP-Abhängigkeiten installieren
composer install

# Node-Abhängigkeiten installieren und Assets bauen
npm install && npm run build

# Umgebungsdatei erstellen
cp .env.example .env
# APP_URL in .env anpassen
```

Dann den Dev-Server starten:

```bash
php artisan serve
```

Im Browser `/install` aufrufen → Formular ausfüllen → fertig.

## Demo-Daten (optional)

Erst `/install` abschließen, dann:

```bash
php artisan db:seed
```

Erstellt einen Admin-Benutzer (`admin@example.com` / `password`), einen Demo-API-Token und 5 Beispiel-Maschinen mit verschiedenen Status.

## Entwicklung

```bash
# Dev-Server starten
php artisan serve

# Vite-Dev-Server (Hot-Reload)
npm run dev
```

## Produktion / Shared Hosting

```bash
# Assets für Produktion bauen (public/build/ wird committet)
npm run build
```

`.env` anpassen:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ihre-domain.de
```

Dann im Browser `/install` aufrufen → Formular ausfüllen → fertig.

### Cron-Job einrichten

```
* * * * * cd /pfad/zum/projekt && php artisan schedule:run >> /dev/null 2>&1
```

Geplante Tasks:
- `machines:mark-stale` — Alle 15 Min: Maschinen ohne Report seit 25h als "stale" markieren
- `reports:prune --days=90` — Täglich: Alte Reports löschen (letzten Report pro Maschine behalten)

## API

### Report senden

```bash
curl -X POST https://ihre-domain.de/api/v1/report \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer IHR-API-TOKEN" \
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

API-Tokens werden im Admin-Bereich unter **API-Tokens** erstellt. Die vollständige API-Dokumentation ist im Admin-Panel unter **API-Doku** einsehbar.

## Artisan-Commands

| Command | Beschreibung |
|---|---|
| `php artisan machines:mark-stale --hours=25` | Inaktive Maschinen als "stale" markieren |
| `php artisan reports:prune --days=90` | Alte Reports bereinigen |

## Lizenz

MIT
