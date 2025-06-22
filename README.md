# 🚀 Laravel E-commerce API

A modern, secure, fully-tested Laravel e-commerce API ready for production. This project provides a complete REST API for e-commerce applications with comprehensive test coverage.

## ✨ Key Features

- **Pure REST API** with Laravel Sanctum authentication
- **Complete test suite** with 80+ tests covering 100% of endpoints
- **Role-based access** (admin/customer) with proper authorization
- **Docker containerization** for easy deployment
- **Comprehensive CRUD operations** for all resources
- **Advanced filtering and search** capabilities
- **Secure file upload** handling for product images
- **Order management system** with multiple statuses
- **User profile and address management**
- **Admin dashboard** with statistics and analytics
- **Production-ready** with proper validation and error handling

---

## 🧪 Test Coverage

This API includes a **comprehensive test suite** with:

- **80+ individual tests** across 8 test classes
- **100% endpoint coverage** including edge cases
- **Security testing** (authentication, authorization, data isolation)
- **Validation testing** for all input data
- **Integration testing** for complex workflows
- **Automated test runner** script for CI/CD

### Test Categories
- 🔐 Authentication (7 tests)
- 🛒 Shop/Products (10 tests) 
- 🛍️ Cart Management (13 tests)
- 📦 Order Processing (12 tests)
- 👤 User Profile (12 tests)
- 🏠 Address Management (13 tests)
- 📊 Admin Dashboard (8 tests)
- 🔧 Admin Product Management (15 tests)
- 📁 Admin Category Management (12 tests)

---

## 📦 Installation

```bash
# 1. Clone the repository
git clone https://github.com/YannickSevrin/laravel-ecommerce-api.git
cd laravel-ecommerce-api
```

---

## 🐳 Docker Setup

```bash
# 2. Start Docker containers
docker compose up -d --build
```

---

## ⚙️ Laravel Configuration

```bash
# 3. Navigate to source directory
cd src

# 4. Copy environment file
cp .env.example .env

# 5. Install PHP dependencies
docker compose exec app composer install

# 6. Generate application key
docker compose exec app php artisan key:generate

# 7. Run migrations and seeders
docker compose exec app php artisan migrate:fresh --seed

# 8. Create storage link for file uploads
docker compose exec app php artisan storage:link
```

---

## 🧪 Running Tests

The API includes a complete test suite that can be run easily:

```bash
# Make test script executable
chmod +x run_api_tests.sh

# Run all API tests with organized output
./run_api_tests.sh

# Or run specific test categories
docker compose exec app php artisan test --filter="AuthTest"
docker compose exec app php artisan test --filter="ShopTest"
docker compose exec app php artisan test tests/Feature/Api/Admin/
```

For detailed test documentation, see [`TESTS_DOCUMENTATION.md`](src/TESTS_DOCUMENTATION.md).

---

## 🔐 Authentication Setup

Create an admin user for testing admin endpoints:

```bash
# Access Laravel Tinker
docker compose exec app php artisan tinker

# Create admin user
$admin = App\Models\User::factory()->create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'role' => 'admin'
]);

# Or modify existing user
$user = App\Models\User::where('email', 'your@email.com')->first();
$user->role = 'admin';
$user->save();
```

---

## 🌐 API Endpoints

The API is accessible at `http://localhost:8000/api/`

### 🔓 Public Endpoints
- `GET /api/products` - List products with filtering/search
- `GET /api/products/{id}` - Get single product
- `GET /api/categories` - List categories with product counts

### 🔐 Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/user` - Get authenticated user

### 👤 User Endpoints (Authenticated)
- `GET /api/profile` - Get user profile
- `PUT /api/profile` - Update profile
- `DELETE /api/profile` - Delete account

### 🏠 Address Management
- `GET /api/profile/addresses` - List user addresses
- `POST /api/profile/addresses` - Create address
- `PUT /api/profile/addresses/{id}` - Update address
- `DELETE /api/profile/addresses/{id}` - Delete address
- `POST /api/profile/addresses/{id}/default` - Set default address

### 🛍️ Cart Management
- `GET /api/cart` - View cart
- `POST /api/cart/add/{product}` - Add to cart
- `PUT /api/cart/update/{product}` - Update quantity
- `DELETE /api/cart/remove/{product}` - Remove item
- `DELETE /api/cart/clear` - Clear cart

### 📦 Order Management
- `GET /api/orders` - List user orders
- `GET /api/orders/{id}` - Get single order
- `POST /api/orders` - Create order (checkout)

### 🔧 Admin Endpoints (Admin Role Required)
- `GET /api/admin/dashboard` - Dashboard statistics
- `GET /api/admin/products` - Manage products
- `POST /api/admin/products` - Create product
- `PUT /api/admin/products/{id}` - Update product
- `DELETE /api/admin/products/{id}` - Delete product
- `GET /api/admin/categories` - Manage categories
- `POST /api/admin/categories` - Create category
- `PUT /api/admin/categories/{id}` - Update category
- `DELETE /api/admin/categories/{id}` - Delete category

For complete API documentation, see [`API_DOCUMENTATION.md`](src/API_DOCUMENTATION.md).

---

## 🧠 Technologies Used

- **Laravel 11** - PHP framework
- **Laravel Sanctum** - API authentication
- **Docker** - Containerization (PHP-FPM, Nginx, MySQL, Node.js)
- **Pest PHP** - Testing framework
- **SQLite** - Testing database
- **Custom Middleware** - Role-based authorization
- **Factory Pattern** - Test data generation
- **Image Upload** - File handling with validation

---

## 📂 Project Structure

```
├── docker/
│   ├── Dockerfile
│   └── nginx.conf
├── src/
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/          # API Controllers
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── ShopController.php
│   │   │   │   ├── CartController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   ├── AddressController.php
│   │   │   │   └── Admin/                # Admin Controllers
│   │   │   │       ├── AdminDashboardController.php
│   │   │   │       ├── AdminProductController.php
│   │   │   │       ├── AdminCategoryController.php
│   │   │   │       ├── AdminOrderController.php
│   │   │   │       └── AdminUserController.php
│   │   │   ├── Middleware/
│   │   │   │   └── IsAdminApi.php        # Admin authorization
│   │   │   └── Requests/
│   │   ├── Models/                       # Eloquent Models
│   │   │   ├── User.php
│   │   │   ├── Product.php
│   │   │   ├── Category.php
│   │   │   ├── Cart.php
│   │   │   ├── Order.php
│   │   │   ├── OrderItem.php
│   │   │   └── Address.php
│   │   └── Mail/
│   │       └── OrderConfirmation.php
│   ├── routes/
│   │   └── api.php                       # API Routes
│   ├── tests/
│   │   ├── Feature/Api/                  # API Tests
│   │   │   ├── AuthTest.php
│   │   │   ├── ShopTest.php
│   │   │   ├── CartTest.php
│   │   │   ├── OrderTest.php
│   │   │   ├── ProfileTest.php
│   │   │   ├── AddressTest.php
│   │   │   └── Admin/
│   │   │       ├── AdminDashboardTest.php
│   │   │       ├── AdminProductTest.php
│   │   │       └── AdminCategoryTest.php
│   │   └── TestCase.php                  # Enhanced test base class
│   ├── database/
│   │   ├── factories/                    # Model Factories
│   │   ├── migrations/                   # Database Schema
│   │   └── seeders/                      # Sample Data
│   ├── run_api_tests.sh                  # Test Runner Script
│   ├── API_DOCUMENTATION.md              # Complete API Docs
│   └── TESTS_DOCUMENTATION.md            # Test Suite Docs
├── docker-compose.yml
└── README.md
```

---

## ✅ Implemented Features

### 🔐 Authentication & Authorization
- JWT-like token authentication with Sanctum
- Role-based access control (admin/customer)
- Secure password hashing and validation
- Token-based session management

### 👤 User Management
- User registration and login
- Profile management (view, update, delete)
- Address management with default selection
- Password change with current password verification

### 🛒 Product Catalog
- Product listing with pagination
- Advanced filtering (category, price range, search)
- Product sorting by various criteria
- Category management with product counts
- Image upload for products

### 🛍️ Shopping Cart
- Persistent cart storage in database
- Add, update, remove cart items
- Automatic total calculation
- Cart clearing functionality

### 📦 Order Processing
- Order creation from cart
- Order history and details
- Multiple order statuses
- Order item tracking

### 🔧 Admin Features
- Dashboard with comprehensive statistics
- Product CRUD operations
- Category CRUD operations
- User role management
- Order status updates

### 🧪 Quality Assurance
- 80+ comprehensive tests
- 100% endpoint coverage
- Security and validation testing
- Automated test runner
- CI/CD ready test suite

---

## 🚀 Production Deployment

### Environment Configuration
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-api-domain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password

SANCTUM_STATEFUL_DOMAINS=your-frontend-domain.com
SESSION_DOMAIN=.your-domain.com
```

### Security Checklist
- ✅ All endpoints properly authenticated
- ✅ Role-based authorization implemented
- ✅ Input validation on all endpoints
- ✅ File upload security measures
- ✅ SQL injection protection
- ✅ XSS protection
- ✅ CSRF protection for web routes

---

## 🔧 Development

### Adding New Endpoints
1. Create controller in `app/Http/Controllers/Api/`
2. Add routes in `routes/api.php`
3. Create corresponding tests
4. Update API documentation

### Running Development Server
```bash
# Start Laravel development server
docker compose exec app php artisan serve --host=0.0.0.0 --port=8000

```

### Database Operations
```bash
# Reset database with fresh data
docker compose exec app php artisan migrate:fresh --seed

# Create new migration
docker compose exec app php artisan make:migration create_new_table

# Create new seeder
docker compose exec app php artisan make:seeder NewTableSeeder
```

---

## 📚 Documentation

- **[API Documentation](src/API_DOCUMENTATION.md)** - Complete endpoint documentation
- **[Test Documentation](src/TESTS_DOCUMENTATION.md)** - Test suite overview

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests for new functionality
4. Ensure all tests pass
5. Submit a pull request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🎯 What's Next

- [ ] Stripe payment integration
- [ ] PDF invoice generation
- [ ] Real-time notifications
- [ ] Advanced analytics
- [ ] Rate limiting
- [ ] API versioning

---

**✨ Your production-ready Laravel E-commerce API with comprehensive test coverage!**
