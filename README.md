# Lead Management System - Backend

A simple **Lead Management System** built with the **Slim Framework** (PHP) and **MySQL**. This backend allows users to submit and manage leads, storing them in a database and notifying an external API when a new lead is created.

---

## 🚀 **Features**
- Lead submission via REST API.
- Data persistence using MySQL.
- Environment variable management with `.env`.
- Error handling and logging with Monolog.
- Dependency Injection (DI) with PHP-DI.
- Slim Framework 4 structure.

---

## 🗂️ **Project Structure**
```plaintext
lead-management-backend/
├── app/                # App configuration and dependencies
│   ├── settings.php
│   ├── dependencies.php
│   └── routes.php
├── public/             # Entry point for the application
│   └── index.php
├── logs/               # Log files
├── vendor/             # Composer dependencies
├── .env                # Environment variables
├── .env.example        # Example env file
├── composer.json       # Project dependencies and scripts
└── README.md           # Project documentation
```

---

## 🛠️ **Setup Instructions**

### 1. **Clone the Repository**
```bash
git clone https://github.com/your-username/lead-management-backend.git
cd lead-management-backend
```

### 2. **Install Dependencies**
```bash
composer install
```

### 3. **Configure Environment Variables**
1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```
2. Update the `.env` file with your database credentials:
   ```ini
   DB_DRIVER=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=lead_management
   DB_USERNAME=root
   DB_PASSWORD=123456
   ```

### 4. **Set Up the Database**
1. Access MySQL:
   ```bash
   mysql -u root -p
   ```
2. Create the database and table:
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

### 5. **Run the Application**
```bash
php -S localhost:8000 -t public
```
✅ Visit [http://localhost:8000](http://localhost:8000) to test the application.

### 6. **Test Database Connection**
Use the `/db-test` route to confirm DB connectivity:
```bash
curl http://localhost:8000/db-test
```
✅ Expected output:
```
Connected to DB: lead_management
```

---

## 📄 **Available Routes**

### ✅ **GET /**
- **Description:** Basic health check.
- **Response:** `Hello from Lead Management Backend!`

### ✅ **GET /db-test**
- **Description:** Tests database connection.
- **Response:** `Connected to DB: lead_management`

### 🔥 **Active Technical Test Routes:**
#### ✅ **POST /leads** - Create a new lead.
- **Description:** Submits a new lead to the database and notifies an external API.
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
  - `201 Created` - Lead successfully created.
  - `400 Bad Request` - Validation error (e.g., missing fields or invalid data).
  - `409 Conflict` - Email already exists.
- **Example CURL Command:**
  ```bash
  curl -X POST http://localhost:8000/leads \
    -H "Content-Type: application/json" \
    -d '{"name": "Jane Doe", "email": "jane.doe@example.com", "phone": "9876543210", "source": "linkedin"}'
  ```

---

#### ✅ **GET /leads** - Retrieve all leads.
- **Description:** Returns a list of all leads stored in the database.
- **Response:**
  ```json
  [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john.doe@example.com",
      "phone": "1234567890",
      "source": "google",
      "created_at": "2024-02-21T14:30:00"
    },
    {
      "id": 2,
      "name": "Jane Doe",
      "email": "jane.doe@example.com",
      "phone": "9876543210",
      "source": "linkedin",
      "created_at": "2024-02-22T09:15:00"
    }
  ]
  ```
- **Example CURL Command:**
  ```bash
  curl http://localhost:8000/leads
  ```

---

#### ✅ **GET /leads/{id}** - Retrieve a single lead by ID.
- **Description:** Fetches the details of a specific lead by its unique ID.
- **URL Parameter:**
  - `id` (integer) - Lead ID.
- **Response:**
  ```json
  {
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com",
    "phone": "1234567890",
    "source": "google",
    "created_at": "2024-02-21T14:30:00"
  }
  ```
- **Errors:**
  - `404 Not Found` - If the lead with the given ID does not exist.
- **Example CURL Command:**
  ```bash
  curl http://localhost:8000/leads/1
  ```

---

## 📝 **Scripts**
- **Start the server:**
  ```bash
  composer start
  ```
  *(Runs `php -S localhost:8080 -t public`)*

- **Run tests:** *(If applicable)*
  ```bash
  composer test
  ```

---

## 🧪 **Testing**
Use tools like **Postman** or **curl** to test API endpoints.

Example to test `/db-test`:
```bash
curl http://localhost:8000/db-test
```

Example to test creating a lead:
```bash
curl -X POST http://localhost:8000/leads \
  -H "Content-Type: application/json" \
  -d '{"name": "Test User", "email": "test.user@example.com", "source": "facebook"}'
```

---

## 🛡️ **Troubleshooting**
- **Database connection issues:**
  - Verify your `.env` settings.
  - Check if MySQL is running.
  - Confirm your database and user permissions.
- **Validation errors:**
  - Ensure all required fields are present in the request body.
  - Check that the `email` is unique and correctly formatted.
- **External API failures:**
  - Check the configured external API URL.
  - Review logs in `logs/app.log` for detailed error messages.
- **Port conflicts:**
  - Change the port if `localhost:8000` is busy.

