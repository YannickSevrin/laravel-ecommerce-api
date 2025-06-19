# 🛍️ Laravel E-commerce Template

This project is a modern, secure, dockerized Laravel e-commerce template ready for production.

## 🚀 Main Features

- Laravel Breeze Authentication (Blade + TailwindCSS)
- Role management (`admin` / `customer`)
- Clean architecture (separated routes, middlewares)
- Docker (PHP, MySQL, NGINX, Node.js)
- ViteJS + TailwindCSS for frontend
- Core E-commerce Models: Product, Category, Order, Cart, Address
- Admin Dashboard with CRUDs (Product, Category, Order, User)
- Ready for deployment

---

## 📦 Installation

```bash
# 1. Clone the repository
git clone https://github.com/YannickSevrin/laravel-ecommerce-template.git
cd laravel-ecommerce-template
```

---

## 🐳 Docker Startup

```bash
# 2. Start Docker containers
docker-compose up -d --build
```

---

## ⚙️ Laravel Configuration

```bash
# 3. Copy environment example
docker-compose exec app cp .env.example .env

# 4. Install PHP dependencies
docker-compose exec app composer install

# 5. Generate application key
docker-compose exec app php artisan key:generate

# 6. Run migrations and seeders
docker-compose exec app php artisan migrate:fresh --seed
```

---

## 🎨 Frontend with Vite + Tailwind

```bash
# 7. Install NodeJS dependencies
docker exec -it node npm install

# 8. Run Vite in development mode
docker exec -it node npm run dev
```

---

## 🔐 Create an admin user (optional)

```bash
# 9. Access Tinker
docker-compose exec app php artisan tinker

# 10. Modify user role
$user = App\Models\User::where('email', 'your@email.com')->first();
$user->role = 'admin';
$user->save();
```

---

## 🌐 Site Access

- Front: [http://localhost:8000](http://localhost:8000)
- Login/Register: `/login`, `/register`
- Admin dashboard: `/admin/dashboard`
  - Manage products
  - Manage categories
  - View and update orders
  - Manage users and roles
  - View admin statistics

---

## 🧠 Technologies Used

- Laravel 11
- Laravel Breeze
- ViteJS
- TailwindCSS
- Docker (php-fpm, nginx, mysql, node)
- Custom middleware (`isAdmin`)

---

## 📂 Custom Structure

```
├── docker/
│   ├── nginx.conf
│   └── Dockerfile
├── src/
│   ├── app/
│   │   ├── Models/         # User, Product, Cart, Order, etc.
│   │   ├── Http/Controllers/Admin/ # Separated admin logic
│   │   ├── Http/Middleware/
│   ├── routes/
│   │   ├── web.php
│   │   ├── admin.php
│   ├── resources/
│   │   ├── views/
│   │   │   ├── admin/
│   │   │   │   ├── dashboard.blade.php
│   │   │   │   ├── products/
│   │   │   │   ├── categories/
│   │   │   │   ├── orders/
│   │   │   │   ├── users/
│   └── ...
├── docker-compose.yml
└── README.md
```

---

## ✅ Completed Admin Features

- Admin dashboard with live statistics
- Product management (CRUD)
- Category management (CRUD)
- Order management (view & status update)
- User management (list, edit roles)

---

## 🛠 Coming Soon

- Frontend cart and checkout
- Stripe payment integration
- Filtered product catalog
- Email notifications
