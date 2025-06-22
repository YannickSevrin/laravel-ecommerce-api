# Laravel E-commerce API

A RESTful API for e-commerce built with Laravel 12 and Laravel Sanctum for authentication.

## Features

- 🔐 **JWT Authentication** with Laravel Sanctum
- 🛍️ **Product Management** (CRUD operations)
- 🛒 **Shopping Cart** functionality
- 📦 **Order Management** system
- 👤 **User Profile** management
- 🏠 **Address Management** for shipping
- 🔧 **Admin Panel** API endpoints
- 📊 **Dashboard Statistics** for admins

## API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/user` - Get authenticated user

### Products (Public)
- `GET /api/products` - List products with filtering
- `GET /api/products/{id}` - Get product details
- `GET /api/categories` - List categories

### Cart (Authenticated)
- `GET /api/cart` - Get cart contents
- `POST /api/cart/add/{product}` - Add to cart
- `PUT /api/cart/update/{product}` - Update quantity
- `DELETE /api/cart/remove/{product}` - Remove from cart

### Orders (Authenticated)
- `GET /api/orders` - List user orders
- `GET /api/orders/{id}` - Get order details
- `POST /api/orders` - Create new order

### Admin (Admin Role Required)
- `GET /api/admin/dashboard` - Admin statistics
- `GET|POST|PUT|DELETE /api/admin/products` - Product management
- `GET|POST|PUT|DELETE /api/admin/categories` - Category management
- `GET|PUT /api/admin/orders` - Order management
- `GET|PUT /api/admin/users` - User management

## Installation

### Requirements
- PHP 8.2+
- Composer
- MySQL/PostgreSQL/SQLite
- Laravel 12

### Setup

1. **Clone the repository**
```bash
git clone <repository-url>
cd laravel-ecommerce-template/src
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment setup**
```bash
cp .env.example .env
docker compose exec app php artisan key:generate
```

4. **Database setup**
```bash
# Configure your database in .env file
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

5. **Storage setup**
```bash
docker compose exec app php artisan storage:link
```

6. **Start the server**
```bash
docker compose exec app php artisan serve
```

The API will be available at `http://localhost:8000/api`

### With Docker

```bash
docker compose up -d
```

## Authentication

This API uses Laravel Sanctum for authentication. After login, include the token in your requests:

```bash
Authorization: Bearer your-token-here
```

## Testing

Run the test suite:
```bash
docker compose exec app php artisan test
```

## API Documentation

For detailed API documentation with examples, see [API_DOCUMENTATION.md](../API_DOCUMENTATION.md)

## Migration from Blade

This project was converted from a Blade-based Laravel application to a pure API. See [MIGRATION_GUIDE.md](../MIGRATION_GUIDE.md) for details.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
