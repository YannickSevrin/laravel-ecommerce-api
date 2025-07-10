<div align="center">

# 🚀 Laravel E-commerce API

[![Laravel](https://img.shields.io/badge/Laravel-11-red?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-ready-blue?style=for-the-badge&logo=docker&logoColor=white)](https://docker.com)
[![Tests](https://img.shields.io/badge/Tests-80+-green?style=for-the-badge&logo=testcafe&logoColor=white)](#-test-coverage)
[![License](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)](LICENSE)

**A modern, secure, and fully-tested Laravel e-commerce REST API ready for production**

[🚀 Quick Start](#-quick-start) • [📖 Documentation](#-api-documentation) • [🧪 Testing](#-testing) • [🐳 Docker](#-docker-deployment) • [🔧 Configuration](#-configuration)

</div>

---

## ✨ Key Features

<table>
<tr>
<td>

### 🔐 **Security First**
- Laravel Sanctum authentication
- Role-based access control
- Data validation & sanitization
- CSRF protection
- Rate limiting

</td>
<td>

### 🧪 **Production Ready**
- 80+ comprehensive tests
- 100% endpoint coverage
- Docker containerization
- CI/CD friendly
- Error handling

</td>
</tr>
<tr>
<td>

### 🛍️ **E-commerce Features**
- Product catalog management
- Shopping cart functionality
- Order processing system
- User profile management
- Address management

</td>
<td>

### 📊 **Admin Dashboard**
- Analytics & statistics
- Product management
- Order management
- User management
- Category management

</td>
</tr>
</table>

---

## 🚀 Quick Start

### Prerequisites

- Docker & Docker Compose
- PHP 8.2+ (for local development)
- Composer (for local development)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/YannickSevrin/laravel-ecommerce-api.git
cd laravel-ecommerce-api

# 2. Start with Docker (Recommended)
docker compose up -d --build

# 3. Setup Laravel
cd src
cp .env.example .env
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan storage:link

# 4. Create admin user
docker compose exec app php artisan tinker
```

In Tinker:
```php
App\Models\User::factory()->create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'role' => 'admin'
]);
```

🎉 **Your API is now running at `http://localhost:8000/api`**

---

## 🧪 Test Coverage

<div align="center">

### 📊 **80+ Tests | 100% Endpoint Coverage**

</div>

| Test Suite | Tests | Coverage |
|------------|-------|----------|
| 🔐 Authentication | 7 tests | All auth endpoints |
| 🛒 Shop/Products | 10 tests | Product catalog |
| 🛍️ Cart Management | 13 tests | Shopping cart |
| 📦 Order Processing | 12 tests | Order lifecycle |
| 👤 User Profile | 12 tests | Profile management |
| 🏠 Address Management | 13 tests | Address CRUD |
| 📊 Admin Dashboard | 8 tests | Admin analytics |
| 🔧 Admin Products | 15 tests | Product management |
| 📁 Admin Categories | 12 tests | Category management |

### Running Tests

```bash
# Run all tests with beautiful output
chmod +x run_api_tests.sh
./run_api_tests.sh

# Run specific test suites
docker compose exec app php artisan test --filter="AuthTest"
docker compose exec app php artisan test --filter="ShopTest"
docker compose exec app php artisan test tests/Feature/Api/Admin/
```

📚 **[View detailed test documentation →](src/TESTS_DOCUMENTATION.md)**

---

## 🌐 API Documentation

<div align="center">

### 📡 **Base URL:** `http://localhost:8000/api`

</div>

### 🔓 Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/products` | List products with filtering/search |
| `GET` | `/products/{id}` | Get product details |
| `GET` | `/categories` | List categories with product counts |

### 🔐 Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/auth/register` | User registration |
| `POST` | `/auth/login` | User login |
| `POST` | `/auth/logout` | User logout |
| `GET` | `/auth/user` | Get authenticated user |

### 👤 User Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/profile` | Get user profile |
| `PUT` | `/profile` | Update profile |
| `DELETE` | `/profile` | Delete account |

### 🏠 Address Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/profile/addresses` | List user addresses |
| `POST` | `/profile/addresses` | Create address |
| `PUT` | `/profile/addresses/{id}` | Update address |
| `DELETE` | `/profile/addresses/{id}` | Delete address |
| `POST` | `/profile/addresses/{id}/default` | Set default address |

### 🛍️ Cart Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/cart` | View cart |
| `POST` | `/cart/add/{product}` | Add to cart |
| `PUT` | `/cart/update/{product}` | Update quantity |
| `DELETE` | `/cart/remove/{product}` | Remove item |
| `DELETE` | `/cart/clear` | Clear cart |

### 📦 Order Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/orders` | List user orders |
| `GET` | `/orders/{id}` | Get order details |
| `POST` | `/orders` | Create order (checkout) |

### 🔧 Admin Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/admin/dashboard` | Dashboard statistics |
| `GET/POST/PUT/DELETE` | `/admin/products` | Product management |
| `GET/POST/PUT/DELETE` | `/admin/categories` | Category management |
| `GET/PUT` | `/admin/orders` | Order management |
| `GET/PUT` | `/admin/users` | User management |

📖 **[View complete API documentation →](src/API_DOCUMENTATION.md)**

---

## 🐳 Docker Deployment

### Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Nginx Proxy   │    │   Laravel App   │    │   MySQL DB      │
│   Port: 8000    │───▶│   PHP-FPM       │───▶│   Port: 3306    │
│                 │    │                 │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Services

| Service | Container | Description |
|---------|-----------|-------------|
| `app` | PHP 8.2-FPM | Laravel application |
| `nginx` | Nginx | Web server & reverse proxy |
| `mysql` | MySQL 8.0 | Database |
| `node` | Node.js | Asset compilation |

### Commands

```bash
# Start all services
docker compose up -d

# View logs
docker compose logs -f app

# Execute Laravel commands
docker compose exec app php artisan migrate
docker compose exec app php artisan cache:clear

# Access containers
docker compose exec app bash
docker compose exec mysql mysql -u root -p
```

---

## 🔧 Configuration

### Environment Variables

```env
# Application
APP_NAME="Laravel E-commerce API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_ecommerce
DB_USERNAME=root
DB_PASSWORD=password

# Authentication
SANCTUM_STATEFUL_DOMAINS=localhost:8000
SESSION_DRIVER=cookie
```

### Key Configuration Files

| File | Description |
|------|-------------|
| `config/sanctum.php` | Authentication settings |
| `config/cors.php` | CORS configuration |
| `config/database.php` | Database connections |
| `docker-compose.yml` | Docker services |
| `docker/Dockerfile` | Application container |

---

## 🛠️ Tech Stack

<div align="center">

| Backend | Database | Authentication | Testing | DevOps |
|---------|----------|---------------|---------|---------|
| ![Laravel](https://img.shields.io/badge/Laravel-11-red?logo=laravel&logoColor=white) | ![MySQL](https://img.shields.io/badge/MySQL-8.0-blue?logo=mysql&logoColor=white) | ![Sanctum](https://img.shields.io/badge/Sanctum-3.0-green?logo=laravel&logoColor=white) | ![Pest](https://img.shields.io/badge/Pest-2.0-yellow?logo=php&logoColor=white) | ![Docker](https://img.shields.io/badge/Docker-ready-blue?logo=docker&logoColor=white) |
| ![PHP](https://img.shields.io/badge/PHP-8.2+-blue?logo=php&logoColor=white) | ![SQLite](https://img.shields.io/badge/SQLite-Testing-lightgrey?logo=sqlite&logoColor=white) | ![JWT](https://img.shields.io/badge/JWT-Token-black?logo=json-web-tokens&logoColor=white) | ![PHPUnit](https://img.shields.io/badge/PHPUnit-10-green?logo=php&logoColor=white) | ![Nginx](https://img.shields.io/badge/Nginx-1.21-green?logo=nginx&logoColor=white) |

</div>

---

## 📁 Project Structure

```
laravel-ecommerce-api/
├── 🐳 docker/                 # Docker configuration
│   ├── Dockerfile
│   └── nginx.conf
├── 📱 src/                    # Laravel application
│   ├── app/
│   │   ├── Http/Controllers/Api/  # API controllers
│   │   ├── Models/               # Eloquent models
│   │   ├── Mail/                 # Email templates
│   │   └── Repositories/         # Repository pattern
│   ├── database/
│   │   ├── migrations/           # Database migrations
│   │   ├── seeders/             # Database seeders
│   │   └── factories/           # Model factories
│   ├── tests/                   # Test suites
│   │   ├── Feature/Api/         # API integration tests
│   │   └── Unit/                # Unit tests
│   └── routes/api.php           # API routes
├── 🐳 docker-compose.yml      # Docker services
├── 📝 API_DOCUMENTATION.md    # Complete API docs
├── 🧪 TESTS_DOCUMENTATION.md  # Testing guide
└── 🚀 run_api_tests.sh        # Test runner script
```

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

<div align="center">

**Made with ❤️ by [Yannick Sevrin](https://github.com/YannickSevrin)**

⭐ **Star this repository if you found it helpful!**

</div>
