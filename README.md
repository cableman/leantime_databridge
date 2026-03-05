# Databridge Plugin

A Leantime plugin that exposes an API endpoint for retrieving tickets filtered by username (email), with optional date range and status filtering. Designed for consumption by remote AI agents and external integrations.

## Installation

1. Place the `Databridge` folder in `app/Plugins/`
2. Go to **Settings > Plugins** in Leantime
3. Find "Databridge" under new plugins and click **Install**
4. Enable the plugin

Or via CLI:

```bash
php bin/leantime plugin:enable Databridge
```

## Authentication

Requires a Leantime API key passed via the `x-api-key` header.

Create an API key in **Settings > API** in the Leantime UI.

## Endpoint

```
POST /api/databridge/tickets
GET  /api/databridge/tickets
```

### Parameters

| Parameter  | Type   | Required | Default | Description                                      |
|------------|--------|----------|---------|--------------------------------------------------|
| `username` | string | Yes      |         | Email/username to filter tickets by               |
| `dateFrom` | string | No       |         | ISO date (`Y-m-d`), filters `dateToFinish >=`     |
| `dateTo`   | string | No       |         | ISO date (`Y-m-d`), filters `dateToFinish <=`     |
| `status`   | string | No       |         | Status type: `NEW`, `INPROGRESS`, `DONE` (case-insensitive) |
| `start`    | int    | No       | 0       | Pagination offset by ticket ID                    |
| `limit`    | int    | No       | 100     | Maximum number of results                         |

### Ticket matching

A ticket is returned if the user is either:
- The **assigned editor** of the ticket, or
- A **collaborator** on the ticket

Milestones are excluded.

## Examples

### POST with JSON body (recommended)

```bash
curl -k -X POST https://leantime.example.com/api/databridge/tickets \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"username": "user@example.com"}'
```

### GET with query parameters

```bash
curl -k "https://leantime.example.com/api/databridge/tickets?username=user@example.com&limit=50" \
  -H "x-api-key: YOUR_API_KEY"
```

### With date filtering

```bash
curl -k -X POST https://leantime.example.com/api/databridge/tickets \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"username": "user@example.com", "dateFrom": "2026-01-01", "dateTo": "2026-12-31"}'
```

### With status filtering

```bash
curl -k -X POST https://leantime.example.com/api/databridge/tickets \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"username": "user@example.com", "status": "inprogress"}'
```

### Combined filters

```bash
curl -k -X POST https://leantime.example.com/api/databridge/tickets \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "user@example.com",
    "status": "inprogress",
    "dateFrom": "2026-01-01",
    "dateTo": "2026-12-31",
    "limit": 10
  }'
```

### Pagination

Use `start` (minimum ticket ID) and `limit` to paginate through results:

```bash
# First page
curl -k -X POST https://leantime.example.com/api/databridge/tickets \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"username": "user@example.com", "start": 0, "limit": 50}'

# Next page (use the last ticket ID + 1 from previous response)
curl -k -X POST https://leantime.example.com/api/databridge/tickets \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"username": "user@example.com", "start": 1051, "limit": 50}'
```

## Response format

```json
{
  "parameters": {
    "username": "user@example.com",
    "dateFrom": "2026-01-01",
    "dateTo": "2026-12-31",
    "status": null,
    "start": 0,
    "limit": 100
  },
  "resultsCount": 3,
  "results": [
    {
      "id": 42,
      "projectId": 1,
      "name": "Fix login bug",
      "status": "INPROGRESS",
      "milestoneId": null,
      "tags": ["bug"],
      "worker": "user@example.com",
      "plannedHours": 4.0,
      "remainingHours": 2.0,
      "dueDate": "2026-03-15T00:00:00.000000Z",
      "resolutionDate": null,
      "modified": "2026-03-01T14:30:00.000000Z"
    }
  ]
}
```

## Error responses

### Missing username (400)

```json
{"error": "The \"username\" parameter is required."}
```

### Invalid API key (401)

```json
{"error": "Invalid API Key"}
```
