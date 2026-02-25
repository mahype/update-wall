---
title: Report Endpoint
order: 2
---

# POST /api/v1/report

Sends an update report for a machine to the dashboard. If the machine does not yet exist, it is automatically created based on the `hostname`.

## Request

```
POST /api/v1/report
Content-Type: application/json
Authorization: Bearer <YOUR-API-TOKEN>
```

### Required Fields

| Field | Type | Description |
|---|---|---|
| `hostname` | `string` | Unique hostname of the machine (max 255 characters) |
| `timestamp` | `string` | Report timestamp (ISO 8601, e.g. `2026-02-25T10:15:00Z`) |
| `total_updates` | `integer` | Total number of available updates (>= 0) |
| `has_security` | `boolean` | Whether security-critical updates are available |
| `checkers` | `array` | Array of checker results (at least 1) |

### Checker Object

Each entry in the `checkers` array describes an update checker (e.g. apt, npm, docker):

| Field | Type | Required | Description |
|---|---|---|---|
| `name` | `string` | Yes | Name of the checker (max 100 characters) |
| `summary` | `string` | Yes | Summary of the result |
| `error` | `string` | No | Error message if the check failed |
| `update_hint` | `string` | No | Optional command to perform the updates (e.g. `sudo apt upgrade`) |
| `updates` | `array` | No | Array of available updates |

### Update Object

Each entry in the `updates` array of a checker:

| Field | Type | Required | Description |
|---|---|---|---|
| `name` | `string` | Yes | Package name (max 255 characters) |
| `current_version` | `string` | Yes | Currently installed version (max 100 characters) |
| `new_version` | `string` | Yes | Available new version (max 100 characters) |
| `type` | `string` | Yes | Update type (see below) |
| `priority` | `string` | Yes | Priority (see below) |
| `source` | `string` | No | Source of the update (max 255 characters) |
| `phasing` | `string` | No | Phasing information (max 100 characters) |

### Allowed Values for `type`

| Value | Description |
|---|---|
| `security` | Security update |
| `regular` | Regular update |
| `plugin` | Plugin update (e.g. WordPress) |
| `theme` | Theme update |
| `core` | Core update (e.g. WordPress core) |
| `image` | Container image update |
| `distro` | Distribution update |

### Allowed Values for `priority`

| Value | Description |
|---|---|
| `critical` | Critical — immediate attention required |
| `high` | High — update soon |
| `normal` | Normal — regular update |
| `low` | Low — can be updated at your convenience |

## Example Request

```bash
curl -X POST https://your-domain.com/api/v1/report \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR-API-TOKEN" \
  -d '{
    "hostname": "webserver-prod",
    "timestamp": "2026-02-25T10:15:00Z",
    "total_updates": 3,
    "has_security": true,
    "checkers": [
      {
        "name": "apt",
        "summary": "3 updates available",
        "update_hint": "sudo apt upgrade",
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

## Successful Response

**Status:** `201 Created`

```json
{
    "status": "ok",
    "report_id": 42,
    "machine_id": 7
}
```

| Field | Type | Description |
|---|---|---|
| `status` | `string` | Always `"ok"` on success |
| `report_id` | `integer` | ID of the created report |
| `machine_id` | `integer` | ID of the machine (newly created or existing) |

## Behavior

- **New machine:** Automatically created based on the `hostname` and associated with the API token.
- **Existing machine:** The report is added to the existing machine.
- **Status calculation:** After saving, the machine status is automatically updated:
  - `has_security: true` → Status **Security** (red)
  - `total_updates > 0` → Status **Updates** (yellow)
  - `total_updates == 0` → Status **OK** (green)
- **Stale detection:** Machines without a report within 25 hours are automatically marked as **Stale**.
