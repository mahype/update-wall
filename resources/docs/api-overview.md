---
title: API-Übersicht
order: 1
---

# API-Übersicht

Die Update Wall API ermöglicht es [Update Watcher](https://github.com/mahype/update-watcher)-Clients, Update-Berichte an das Dashboard zu senden. Die API ist REST-basiert und verwendet JSON als Datenformat.

## Base-URL

```
https://ihre-domain.de/api/v1
```

## Authentifizierung

Alle API-Anfragen müssen mit einem **Bearer Token** im `Authorization`-Header authentifiziert werden:

```
Authorization: Bearer <IHR-API-TOKEN>
```

API-Tokens werden im Admin-Bereich unter **API-Tokens** erstellt. Der Klartext-Token wird **nur einmalig** nach der Erstellung angezeigt — kopieren Sie ihn sofort und bewahren Sie ihn sicher auf.

### Token-Eigenschaften

| Eigenschaft | Beschreibung |
|---|---|
| Speicherung | SHA-256-Hash in der Datenbank (Klartext wird nicht gespeichert) |
| Ablaufdatum | Optional, kann beim Erstellen gesetzt werden |
| Widerruf | Tokens können jederzeit im Admin-Bereich widerrufen werden |

## Rate-Limiting

Die API ist standardmäßig auf **60 Anfragen pro Minute** pro IP-Adresse begrenzt. Bei Überschreitung erhalten Sie einen `429 Too Many Requests`-Fehler.

## Fehler-Antworten

Die API gibt Fehler im folgenden Format zurück:

### 401 Unauthorized — Ungültiger oder fehlender Token

```json
{
    "message": "Unauthorized"
}
```

### 422 Unprocessable Entity — Validierungsfehler

```json
{
    "message": "The hostname field is required.",
    "errors": {
        "hostname": ["The hostname field is required."],
        "checkers": ["The checkers field is required."]
    }
}
```

### 429 Too Many Requests — Rate-Limit überschritten

```json
{
    "message": "Too Many Attempts."
}
```

## Verfügbare Endpunkte

| Methode | Pfad | Beschreibung |
|---|---|---|
| `POST` | `/api/v1/report` | Update-Bericht senden |

Details zum Report-Endpunkt finden Sie unter [Report-Endpunkt](/admin/docs/api-report-endpoint).
