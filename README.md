## PhoneAI - Intelligent Contact Lookup API  

PhoneAI is a ***Workerman-based API*** designed for programmatic contact lookups using phone numbers. It integrates with external APIs to retrieve contact details and leverages ***OpenAI*** to generate descriptions based on the retrieved data. The system caches results in `datasheet.json`, updating them automatically based on the expiration period set in `.env`.  

---

### Features  

- ***Phone Number Lookup*** → Retrieve contact details via API.  
- ***Intelligent Caching*** → Stores contact details locally and refreshes them after the configured period (`DATA_EXPIRY_MONTHS` in `.env`).  
- ***AI-Generated Descriptions*** → Uses OpenAI to create detailed insights about contacts.  
- ***Workerman HTTP Server*** → High-performance, asynchronous PHP API server.  
- ***Environment Configuration*** → Customizable API settings via `.env`.  
- ***Minimal Dependencies*** → Lightweight and optimized for efficiency.  

---

### Configuration  

#### 1. Set Up Environment Variables  
Copy `.env.example` to `.env` and update values accordingly:  

```sh
cp .env.example .env
```

| Variable             | Description                                      | Default      |
|----------------------|--------------------------------------------------|-------------|
| `HOST`              | API server host                                  | `localhost` |
| `PORT`              | API server port                                  | `8080`      |
| `API_URL`           | External API for contact lookup                  | *Required*  |
| `OPENAI_KEY`        | API key for OpenAI                               | *Required*  |
| `OPENAI_URL`        | OpenAI API endpoint                              | *Required*  |
| `OPENAI_MODEL`      | OpenAI API model                              | *Required*  |
| `OPENAI_CONTENT`    | OpenAI API content                              | *Required*  |
| `DATA_EXPIRY_MONTHS`| Number of months before cached data expires      | `1`         |

---

#### 2. Set Up Datasheet Cache  
Copy `datasheet.example.json` to `datasheet.json` to enable caching:  

```sh
cp datasheet.example.json datasheet.json
```

The `datasheet.json` file stores previously searched contacts to optimize API performance.  

---

### Running the API  

#### Start Workerman (Foreground Mode)  
```sh
php index.php start
```

#### Start Workerman (Daemon Mode - Background Process)  
```sh
php index.php start -d
```

Once started, the API is accessible via HTTP requests.  

---

### API Endpoints  

| Method | Endpoint         | Description                           |
|--------|----------------|--------------------------------------|
| `GET`  | `/test`         | Check API availability.            |
| `GET`  | `/{phoneNumber}` | Retrieve contact details.          |

---

### API Commands  

| Command                        | Description                              |
|---------------------------------|------------------------------------------|
| `php index.php start`          | Start the API server.                    |
| `php index.php start -d`       | Start the server in daemon mode.         |
| `php index.php status`         | Show API server status.                  |
| `php index.php status -d`      | Show server status in daemon mode.       |
| `php index.php connections`    | Display active connections.              |
| `php index.php stop`           | Stop the API server.                     |
| `php index.php stop -g`        | Stop all processes globally.             |
| `php index.php restart`        | Restart the API server.                  |
| `php index.php reload`         | Reload without downtime.                 |
| `php index.php reload -g`      | Reload all processes globally.           |

---

### API Request Example  

```sh
curl http://localhost:8080/6281234567890
```

***Response:***  
```json
{
  "timestamp": 1712385600,
  "nomor": "6281234567890",
  "total_tag": 2,
  "tag": "Business # Technology",
  "description": "John Doe is a tech entrepreneur with experience in software development..."
}
```

---

### Notes  
- `.env` must be correctly configured before running the API.  
- `datasheet.json` must exist for caching to work.  
- If using a custom port, update API requests accordingly.