# 📱 Laravel Source Code

This directory contains the Laravel application source code for the e-commerce API.

## 🗂️ Directory Structure

```
src/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/          # API Controllers
│   │   │   ├── AuthController.php           # Authentication endpoints
│   │   │   ├── ShopController.php           # Product catalog
│   │   │   ├── CartController.php           # Shopping cart
│   │   │   ├── OrderController.php          # Order management
│   │   │   ├── ProfileController.php        # User profile
│   │   │   ├── AddressController.php        # Address management
│   │   │   └── Admin/                       # Admin controllers
│   │   │       ├── AdminDashboardController.php
│   │   │       ├── AdminProductController.php
│   │   │       ├── AdminCategoryController.php
│   │   │       ├── AdminOrderController.php
│   │   │       └── AdminUserController.php
│   │   ├── Middleware/
│   │   │   └── IsAdminApi.php        # Admin authorization middleware
│   │   └── Requests/
│   │       └── ProfileUpdateRequest.php
│   ├── Models/                       # Eloquent Models
│   │   ├── User.php                 # User model with role support
│   │   ├── Product.php              # Product with categories
│   │   ├── Category.php             # Product categories
│   │   ├── Cart.php                 # Shopping cart items
│   │   ├── Order.php                # Order management
│   │   ├── OrderItem.php            # Order line items
│   │   └── Address.php              # User addresses
│   ├── Mail/
│   │   └── OrderConfirmation.php    # Order confirmation email
│   ├── Services/                    # Business logic services
│   └── Repositories/                # Data access layer
├── database/
│   ├── factories/                   # Model factories for testing
│   │   ├── UserFactory.php
│   │   ├── ProductFactory.php
│   │   └── CategoryFactory.php
│   ├── migrations/                  # Database schema
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2025_06_17_164608_add_role_to_users_table.php
│   │   ├── 2025_06_18_162055_create_products_table.php
│   │   ├── 2025_06_18_162602_create_categories_table.php
│   │   ├── 2025_06_18_162650_create_orders_table.php
│   │   ├── 2025_06_18_162916_create_order_items_table.php
│   │   ├── 2025_06_18_172007_create_addresses_table.php
│   │   ├── 2025_06_18_172007_create_carts_table.php
│   │   └── 2025_06_22_173002_create_personal_access_tokens_table.php
│   └── seeders/                     # Sample data
│       ├── UserSeeder.php
│       ├── ProductSeeder.php
│       ├── CategorySeeder.php
│       ├── OrderSeeder.php
│       └── DatabaseSeeder.php
├── tests/
│   ├── Feature/Api/                 # API Integration tests
│   │   ├── AuthTest.php            # Authentication tests
│   │   ├── ShopTest.php            # Product catalog tests
│   │   ├── CartTest.php            # Cart functionality tests
│   │   ├── OrderTest.php           # Order processing tests
│   │   ├── ProfileTest.php         # User profile tests
│   │   ├── AddressTest.php         # Address management tests
│   │   └── Admin/                  # Admin endpoint tests
│   │       ├── AdminDashboardTest.php
│   │       ├── AdminProductTest.php
│   │       └── AdminCategoryTest.php
│   └── Unit/                       # Unit tests
├── routes/
│   └── api.php                     # API route definitions
├── config/                         # Laravel configuration
│   ├── app.php
│   ├── database.php
│   ├── sanctum.php
│   └── ...
├── composer.json                   # PHP dependencies
├── artisan                        # Laravel command line tool
└── run_api_tests.sh               # Test runner script
```

## 🚀 Quick Development Commands

```bash
# Install dependencies
composer install

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Create symbolic link for storage
php artisan storage:link

# Run tests
php artisan test
./run_api_tests.sh

# Create new resources
php artisan make:controller Api/NewController
php artisan make:model NewModel -mfs
php artisan make:test NewTest
```

## 🏗️ Architecture Patterns

### Controllers
- **API Controllers**: Located in `app/Http/Controllers/Api/`
- **Admin Controllers**: Separated in `Admin/` subdirectory
- **Middleware**: `IsAdminApi` for role-based access control

### Models
- **Eloquent ORM**: All models extend Laravel's base Model
- **Relationships**: Properly defined with foreign keys
- **Factories**: Available for all models for testing

### Authentication
- **Laravel Sanctum**: Token-based authentication
- **Role-based Access**: User roles (admin/customer)
- **Middleware Protection**: Routes protected by auth middleware

### Testing
- **Feature Tests**: Complete API endpoint testing
- **Test Database**: SQLite for isolated testing
- **Factories**: Generate test data consistently

## 🔧 Configuration Files

| File | Purpose |
|------|---------|
| `config/sanctum.php` | Authentication configuration |
| `config/cors.php` | Cross-origin resource sharing |
| `config/database.php` | Database connections |
| `config/app.php` | Application settings |
| `routes/api.php` | API route definitions |

## 📊 Database Schema

### Core Tables
- `users` - User accounts with role support
- `products` - Product catalog
- `categories` - Product categories
- `orders` - Order management
- `order_items` - Order line items
- `carts` - Shopping cart items
- `addresses` - User shipping addresses
- `personal_access_tokens` - API authentication tokens

### Relationships
```
User (1) ──── (N) Address
User (1) ──── (N) Cart
User (1) ──── (N) Order
Order (1) ──── (N) OrderItem
Product (1) ──── (N) OrderItem
Product (1) ──── (N) Cart
Category (1) ──── (N) Product
```

## 🧪 Test Coverage

The application has comprehensive test coverage:

- **80+ individual tests** across all features
- **100% API endpoint coverage**
- **Authentication & authorization testing**
- **Data validation testing**
- **Business logic testing**

Run tests with:
```bash
# All tests
php artisan test

# Specific test
php artisan test --filter=AuthTest

# With coverage
php artisan test --coverage
```

## 📚 Additional Documentation

- **[Complete API Documentation](../API_DOCUMENTATION.md)**
- **[Test Documentation](../TESTS_DOCUMENTATION.md)**
- **[Main README](../README.md)** - Installation and deployment guide

---

**Note**: This directory contains the Laravel application source code. For installation instructions and deployment guide, see the [main README](../README.md).
