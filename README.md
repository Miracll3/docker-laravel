# 📱 Phone Number Generator & Validator

This is a full-stack PHP-based phone number generator and validation system using **Docker**, **MongoDB**, and the **libphonenumber** library.


## 🛠 Prerequisites

- Docker
- Docker Compose

---

## 🔧 Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/Miracll3/docker-laravel
cd docker-laravel
```

### 2. Environment Variables

Create a .env file in the backend/ directory with:

```bash
DB_CONNECTION=mongodb
DB_HOST=mongodb
DB_PORT=27017
DB_DATABASE=phone
DB_USERNAME=
DB_PASSWORD=
```

### 3. Start the Application

Run the following command from the project root:

```bash
docker-compose up --build
```

This will spin up:

**PHP Frontend (laravel)** server at: http://localhost:8080

**API Backend (laravel)** server at: http://localhost:9000

**MongoDB** database on port: 27017


### 4. Running Unit Tests

```bash
docker exec -it laravel_backend bash

php artisan test
```

