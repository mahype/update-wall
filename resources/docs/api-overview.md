---
title: API Overview
order: 1
---

# API Overview

The Update Wall API allows [Update Watcher](https://github.com/mahype/update-watcher) clients to send update reports to the dashboard. The API is REST-based and uses JSON as the data format.

## Base URL

```
https://your-domain.com/api/v1
```

## Authentication

All API requests must be authenticated with a **Bearer Token** in the `Authorization` header:

```
Authorization: Bearer <YOUR-API-TOKEN>
```

API tokens are created in the admin panel under **API Tokens**. The plain-text token is displayed **only once** after creation — copy it immediately and store it securely.

### Token Properties

| Property | Description |
|---|---|
| Storage | SHA-256 hash in the database (plain text is not stored) |
| Expiration | Optional, can be set during creation |
| Revocation | Tokens can be revoked at any time in the admin panel |

## Rate Limiting

The API is limited to **60 requests per minute** per IP address by default. If exceeded, you will receive a `429 Too Many Requests` error.

## Error Responses

The API returns errors in the following format:

### 401 Unauthorized — Invalid or missing token

```json
{
    "message": "Unauthorized"
}
```

### 422 Unprocessable Entity — Validation error

```json
{
    "message": "The hostname field is required.",
    "errors": {
        "hostname": ["The hostname field is required."],
        "checkers": ["The checkers field is required."]
    }
}
```

### 429 Too Many Requests — Rate limit exceeded

```json
{
    "message": "Too Many Attempts."
}
```

## Available Endpoints

| Method | Path | Description |
|---|---|---|
| `POST` | `/api/v1/report` | Send an update report |

For details on the report endpoint, see [Report Endpoint](/admin/docs/api-report-endpoint).
