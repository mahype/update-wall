---
title: Watcher Setup
order: 3
---

# Setting Up the Update Watcher

The [Update Watcher](https://github.com/mahype/update-watcher) is a tool that periodically checks whether updates are available for your systems and sends the results to the Update Wall dashboard.

## Prerequisites

- Update Watcher installed ([GitHub Repository](https://github.com/mahype/update-watcher))
- A valid API token (create one in the admin panel under **API Tokens**)
- Network access to the Update Wall URL

## Configuration

### 1. Create an API Token

1. Log in as an administrator
2. Navigate to **API Tokens** in the sidebar
3. Click **Create Token**
4. Choose a descriptive name (e.g. "Webserver Prod")
5. Optional: Set an expiration date
6. **Important:** Copy the displayed token immediately — it is only shown once!

### 2. Configure the Webhook URL

Enter the webhook URL in your Update Watcher configuration:

```
https://your-domain.com/api/v1/report
```

### 3. Configure Authentication

The API token must be sent as a Bearer token in the Authorization header:

```
Authorization: Bearer <YOUR-API-TOKEN>
```

### 4. Configuration Example

Depending on the Update Watcher version, the configuration may vary. Here is a general example:

```json
{
    "webhook": {
        "url": "https://your-domain.com/api/v1/report",
        "method": "POST",
        "headers": {
            "Authorization": "Bearer YOUR-API-TOKEN-HERE",
            "Content-Type": "application/json"
        }
    }
}
```

## Testing the Connection

Verify the connection with a manual curl request:

```bash
curl -X POST https://your-domain.com/api/v1/report \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR-API-TOKEN" \
  -d '{
    "hostname": "test-machine",
    "timestamp": "2026-02-25T12:00:00Z",
    "total_updates": 0,
    "has_security": false,
    "checkers": [
      {
        "name": "apt",
        "summary": "System is up to date",
        "update_hint": "sudo apt upgrade"
      }
    ]
  }'
```

**Expected response:**

```json
{
    "status": "ok",
    "report_id": 1,
    "machine_id": 1
}
```

## Troubleshooting

### 401 Unauthorized

- Check that the token was copied correctly
- Make sure the token has not been revoked or expired
- The header must be exactly `Authorization: Bearer <TOKEN>`

### 422 Validation Error

- Check that all required fields are present (`hostname`, `timestamp`, `total_updates`, `has_security`, `checkers`)
- Make sure `checkers` contains at least one entry
- Verify the allowed values for `type` and `priority`

### 429 Too Many Requests

- The API is limited to 60 requests per minute
- Wait a moment and try again

### Connection Error

- Verify the URL (including `/api/v1/report`)
- Make sure HTTPS is correctly configured
- Check firewall rules
