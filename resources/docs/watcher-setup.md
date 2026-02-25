---
title: Watcher einrichten
order: 3
---

# Update Watcher einrichten

Der [Update Watcher](https://github.com/mahype/update-watcher) ist ein Tool, das regelmäßig prüft, ob Updates für Ihre Systeme verfügbar sind, und die Ergebnisse an das Update Wall Dashboard sendet.

## Voraussetzungen

- Update Watcher installiert ([GitHub Repository](https://github.com/mahype/update-watcher))
- Ein gültiger API-Token (im Admin-Bereich unter **API-Tokens** erstellen)
- Netzwerkzugriff auf die Update Wall URL

## Konfiguration

### 1. API-Token erstellen

1. Melden Sie sich als Administrator an
2. Navigieren Sie zu **API-Tokens** in der Sidebar
3. Klicken Sie auf **Token erstellen**
4. Vergeben Sie einen aussagekräftigen Namen (z.B. "Webserver Prod")
5. Optional: Setzen Sie ein Ablaufdatum
6. **Wichtig:** Kopieren Sie den angezeigten Token sofort — er wird nur einmal angezeigt!

### 2. Webhook-URL konfigurieren

Tragen Sie in der Konfiguration des Update Watchers die Webhook-URL ein:

```
https://ihre-domain.de/api/v1/report
```

### 3. Authentifizierung konfigurieren

Der API-Token muss als Bearer Token im Authorization-Header gesendet werden:

```
Authorization: Bearer <IHR-API-TOKEN>
```

### 4. Konfigurationsbeispiel

Je nach Update Watcher-Version kann die Konfiguration unterschiedlich aussehen. Hier ein allgemeines Beispiel:

```json
{
    "webhook": {
        "url": "https://ihre-domain.de/api/v1/report",
        "method": "POST",
        "headers": {
            "Authorization": "Bearer IHR-API-TOKEN-HIER",
            "Content-Type": "application/json"
        }
    }
}
```

## Testen der Verbindung

Überprüfen Sie die Verbindung mit einem manuellen curl-Aufruf:

```bash
curl -X POST https://ihre-domain.de/api/v1/report \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer IHR-API-TOKEN" \
  -d '{
    "hostname": "test-maschine",
    "timestamp": "2026-02-25T12:00:00Z",
    "total_updates": 0,
    "has_security": false,
    "checkers": [
      {
        "name": "apt",
        "summary": "System is up to date"
      }
    ]
  }'
```

**Erwartete Antwort:**

```json
{
    "status": "ok",
    "report_id": 1,
    "machine_id": 1
}
```

## Fehlerbehebung

### 401 Unauthorized

- Prüfen Sie, ob der Token korrekt kopiert wurde
- Stellen Sie sicher, dass der Token nicht widerrufen oder abgelaufen ist
- Der Header muss exakt `Authorization: Bearer <TOKEN>` lauten

### 422 Validation Error

- Prüfen Sie, ob alle Pflichtfelder vorhanden sind (`hostname`, `timestamp`, `total_updates`, `has_security`, `checkers`)
- Stellen Sie sicher, dass `checkers` mindestens einen Eintrag enthält
- Überprüfen Sie die erlaubten Werte für `type` und `priority`

### 429 Too Many Requests

- Die API ist auf 60 Anfragen pro Minute limitiert
- Warten Sie einen Moment und versuchen Sie es erneut

### Verbindungsfehler

- Überprüfen Sie die URL (inklusive `/api/v1/report`)
- Stellen Sie sicher, dass HTTPS korrekt konfiguriert ist
- Prüfen Sie Firewall-Regeln
