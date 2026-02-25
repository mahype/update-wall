---
title: Report-Endpunkt
order: 2
---

# POST /api/v1/report

Sendet einen Update-Bericht für eine Maschine an das Dashboard. Falls die Maschine noch nicht existiert, wird sie automatisch anhand des `hostname` angelegt.

## Request

```
POST /api/v1/report
Content-Type: application/json
Authorization: Bearer <IHR-API-TOKEN>
```

### Pflichtfelder

| Feld | Typ | Beschreibung |
|---|---|---|
| `hostname` | `string` | Eindeutiger Hostname der Maschine (max. 255 Zeichen) |
| `timestamp` | `string` | Zeitstempel des Reports (ISO 8601, z.B. `2026-02-25T10:15:00Z`) |
| `total_updates` | `integer` | Gesamtanzahl verfügbarer Updates (>= 0) |
| `has_security` | `boolean` | Ob sicherheitskritische Updates vorhanden sind |
| `checkers` | `array` | Array von Checker-Ergebnissen (mindestens 1) |

### Checker-Objekt

Jeder Eintrag im `checkers`-Array beschreibt einen Update-Checker (z.B. apt, npm, docker):

| Feld | Typ | Pflicht | Beschreibung |
|---|---|---|---|
| `name` | `string` | Ja | Name des Checkers (max. 100 Zeichen) |
| `summary` | `string` | Ja | Zusammenfassung des Ergebnisses |
| `error` | `string` | Nein | Fehlermeldung, falls der Check fehlgeschlagen ist |
| `updates` | `array` | Nein | Array von verfügbaren Updates |

### Update-Objekt

Jeder Eintrag im `updates`-Array eines Checkers:

| Feld | Typ | Pflicht | Beschreibung |
|---|---|---|---|
| `name` | `string` | Ja | Paketname (max. 255 Zeichen) |
| `current_version` | `string` | Ja | Aktuell installierte Version (max. 100 Zeichen) |
| `new_version` | `string` | Ja | Verfügbare neue Version (max. 100 Zeichen) |
| `type` | `string` | Ja | Update-Typ (siehe unten) |
| `priority` | `string` | Ja | Priorität (siehe unten) |
| `source` | `string` | Nein | Quelle des Updates (max. 255 Zeichen) |
| `phasing` | `string` | Nein | Phasing-Information (max. 100 Zeichen) |

### Erlaubte Werte für `type`

| Wert | Beschreibung |
|---|---|
| `security` | Sicherheitsupdate |
| `regular` | Reguläres Update |
| `plugin` | Plugin-Update (z.B. WordPress) |
| `theme` | Theme-Update |
| `core` | Core-Update (z.B. WordPress-Core) |
| `image` | Container-Image-Update |
| `distro` | Distributions-Update |

### Erlaubte Werte für `priority`

| Wert | Beschreibung |
|---|---|
| `critical` | Kritisch — sofortige Aufmerksamkeit erforderlich |
| `high` | Hoch — zeitnah aktualisieren |
| `normal` | Normal — reguläres Update |
| `low` | Niedrig — kann bei Gelegenheit aktualisiert werden |

## Beispiel-Request

```bash
curl -X POST https://ihre-domain.de/api/v1/report \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer IHR-API-TOKEN" \
  -d '{
    "hostname": "webserver-prod",
    "timestamp": "2026-02-25T10:15:00Z",
    "total_updates": 3,
    "has_security": true,
    "checkers": [
      {
        "name": "apt",
        "summary": "3 updates available",
        "updates": [
          {
            "name": "libssl3",
            "current_version": "3.0.2-1",
            "new_version": "3.0.2-2",
            "type": "security",
            "priority": "critical"
          },
          {
            "name": "nginx",
            "current_version": "1.22.0-1",
            "new_version": "1.22.1-1",
            "type": "regular",
            "priority": "normal"
          },
          {
            "name": "curl",
            "current_version": "7.88.0-1",
            "new_version": "7.88.1-1",
            "type": "security",
            "priority": "high"
          }
        ]
      }
    ]
  }'
```

## Erfolgreiche Antwort

**Status:** `201 Created`

```json
{
    "status": "ok",
    "report_id": 42,
    "machine_id": 7
}
```

| Feld | Typ | Beschreibung |
|---|---|---|
| `status` | `string` | Immer `"ok"` bei Erfolg |
| `report_id` | `integer` | ID des erstellten Reports |
| `machine_id` | `integer` | ID der Maschine (neu erstellt oder bestehend) |

## Verhalten

- **Neue Maschine:** Wird automatisch anhand des `hostname` erstellt und dem API-Token zugeordnet.
- **Bestehende Maschine:** Der Report wird der existierenden Maschine hinzugefügt.
- **Status-Berechnung:** Nach dem Speichern wird der Maschinen-Status automatisch aktualisiert:
  - `has_security: true` → Status **Security** (rot)
  - `total_updates > 0` → Status **Updates** (gelb)
  - `total_updates == 0` → Status **OK** (grün)
- **Stale-Erkennung:** Maschinen ohne Report innerhalb von 25 Stunden werden automatisch als **Stale** markiert.
