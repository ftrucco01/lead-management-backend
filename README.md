# Lead Management System - Backend

A simple **Lead Management System** built with the **Slim Framework** (PHP) and **MySQL**. This backend allows users to submit and manage leads, storing them in a database and notifying an external API (e.g., Slack) when a new lead is created.

---

## 🚀 **Features**
- Lead submission via REST API.
- Data persistence using MySQL.
- Environment variable management with `.env`.
- Error handling and logging.
- Dependency Injection (DI) with PHP-DI.
- Slim Framework 4 structure.
- **Automatic notifications to external APIs (e.g., Slack) when a new lead is created.**
- **Dockerized backend and database for easy deployment.**

---

## 🗂️ **Project Structure**
```plaintext
lead-management-backend/
├── src/                # Application logic and services
│   └── Services/
│       ├── LeadService.php           # Handles lead processing and validation
│       └── NotificationService.php   # Handles notifications to external systems (e.g., Slack)
├── public/             # Entry point for the application
│   └── index.php
├── logs/               # Log files
├── vendor/             # Composer dependencies
├── .env                # Environment variables
├── .env.example        # Example env file
├── docker-compose.yml  # Docker configuration
├── Dockerfile          # Docker build instructions
├── init.sql            # Database initialization script
├── composer.json       # Project dependencies and scripts
└── README.md           # Project documentation
```

---

## 🛠️ **Setup Instructions**

### 📦 **Prerequisites:**
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running.
- [Composer](https://getcomposer.org/) installed (if running without Docker).

---

## 🐳 **Running the Application with Docker** *(Recommended)*

### 🚀 **Steps to Run with Docker:**
1. **Clone the Repository:**
```bash
git clone https://github.com/ftrucco01/lead-management-backend.git
cd lead-management-backend
```

2. **Copy the Environment Variables File:**
```bash
cp .env.example .env
```

3. **Build and Start the Containers:**
```bash
docker compose up --build
```

✅ This will:
- Start a **PHP + Apache** container serving the Slim backend on port **8080**.
- Start a **MySQL** container with the `leads_db` database.
- Automatically create the `leads` table using the `init.sql` script.

### 🌐 **Access the Application:**
- **Backend API:** [http://localhost:8080](http://localhost:8080)
- **MySQL Database:** Accessible on port `3306`.

### 🛑 **Stopping the Containers:**
```bash
docker compose down
```
✅ This will stop and remove the containers.

For a full cleanup (including database volumes):
```bash
docker compose down -v
```

---

## 🛠️ **Running the Application Without Docker** *(Alternative Option)*

### 1. **Install Dependencies:**
```bash
composer install
```

### 2. **Configure Environment Variables:**
```bash
cp .env.example .env
```
Edit the `.env` file with your local database credentials:
```ini
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=lead_management
DB_USERNAME=root
DB_PASSWORD=123456

EXTERNAL_API_URL=https://mock-api.com/marketing-webhooks
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/your-slack-webhook-url
```

### 3. **Set Up the Database:**
```bash
mysql -u root -p
```
Then run:
```sql
CREATE DATABASE IF NOT EXISTS lead_management;
USE lead_management;

CREATE TABLE leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    source ENUM('facebook', 'google', 'linkedin', 'manual') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4. **Run the Application:**
```bash
php -S localhost:8080 -t public
```
✅ Access the app at [http://localhost:8080](http://localhost:8080).

---

## 📄 **Available Routes**

### ✅ **GET /**  
- **Description:** Basic health check.  
- **Response:** `Hello from Lead Management Backend!`

### ✅ **GET /db-test**  
- **Description:** Tests database connection.  
- **Response:** `Connected to DB: leads_db`

### ✅ **POST /leads** - Create a new lead.
- **Request Body:**
```json
{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "phone": "1234567890",
  "source": "google"
}
```
- **Responses:**
  - `201 Created`: Lead created successfully.
  - `400 Bad Request`: Validation error.
  - `409 Conflict`: Email already exists.

- **Example cURL Command:**
```bash
curl -X POST http://localhost:8080/leads \
  -H "Content-Type: application/json" \
  -d '{"name": "Jane Doe", "email": "jane.doe@example.com", "phone": "9876543210", "source": "linkedin"}'
```

---

## 💻 **Code Overview**

### 📁 **`LeadService.php`** *(Located in `src/Services/`)*
- **Purpose:** Handles lead processing, validation, and insertion into the database.
- **Key Features:**
  - Validates input data (name, email, phone, source).
  - Inserts valid leads into the `leads` table.
  - Invokes `NotificationService` to send notifications upon successful lead creation.

#### 📝 **Code Highlights:**
- Ensures names are between 3 and 50 characters.
- Validates unique and properly formatted emails.
- Restricts sources to "facebook", "google", "linkedin", or "manual".
- Returns the inserted lead ID upon success.

---

### 📁 **`NotificationService.php`** *(Located in `src/Services/`)*
- **Purpose:** Handles sending notifications to external systems (e.g., Slack) with retry logic.
- **Key Features:**
  - Sends HTTP POST requests to external APIs.
  - Retries failed requests up to **3 times** with **2-second intervals**.
  - Logs all attempts and errors.
  - Formats Slack notifications into user-friendly messages.

#### 📝 **Slack Notification Example Message:**
```
🚀 *New Lead Created!*
Lead ID: 3 | Name: Jane Doe | Email: jane.doe@example.com | Phone: 1234567890 | Source: Google
```

---

## 📢 **Notifications**

When a new lead is created, the system sends a **notification** to the configured external API or Slack webhook.

### 🔔 **Notification Workflow:**
1. The lead is validated and inserted into the database.
2. `NotificationService` sends a POST request to `EXTERNAL_API_URL` or `SLACK_WEBHOOK_URL`.
3. If the request fails with a `400` or `500` response:
   - Retries **3 times** with **2-second intervals**.
   - Logs each failed attempt to `logs/app.log`.

### 📝 **Notification Payload:**
```json
{
  "lead_id": 2,
  "name": "Jane Doe",
  "email": "jane.doe@example.com",
  "phone": "9876543210",
  "source": "linkedin"
}
```

### 📨 **Example Slack Notification:**
> 🚀 *New Lead Created!*  
> | Lead ID: 2 | Name: Jane Doe | Email: jane.doe@example.com | Phone: 9876543210 | Source: Linkedin |

---

## 🗒️ **Logs**
- Logs are stored in the `logs/` directory.
- Example failure log entry:
```plaintext
[2024-02-22 17:15:00] slim-app.ERROR: Failed to notify external system after 3 attempts.
```

---

## 🧪 **Testing the Application**

### ✅ **Test Database Connection:**
```bash
curl http://localhost:8080/db-test
```
✅ Expected response: `Connected to DB: leads_db`

### ✅ **Test Lead Submission:**
```bash
curl -X POST http://localhost:8080/leads \
  -H "Content-Type: application/json" \
  -d '{"name": "Test User", "email": "test.user@example.com", "phone": "123456789", "source": "google"}'
```
✅ Expected response:
```json
{
  "status": "success",
  "message": "Lead created successfully",
  "lead_id": "1"
}
```
✅ **Slack notification example:**
> 🚀 *New Lead Created!*  
> | Lead ID: 1 | Name: Test User | Email: test.user@example.com | Phone: 123456789 | Source: Google |

---

## 📝 **Environment Variables (`.env.example`)**
```ini
DB_DRIVER=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=leads_db
DB_USERNAME=root
DB_PASSWORD=root

EXTERNAL_API_URL=https://mock-api.com/marketing-webhooks
```

---

## 🚀 **Conclusion**
✅ This project provides a fully functional **Lead Management System** with:
- RESTful API for lead submissions.
- MySQL database integration.
- External API and Slack notifications with retry logic.
- **Docker support** for easy setup and deployment.