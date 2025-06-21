
# 🛍️ Laravel E-commerce Template

This project is a modern, secure, dockerized Laravel e-commerce template ready for production.

## 🚀 Main Features

- Laravel Breeze Authentication (Blade + TailwindCSS)
- Role management (`admin` / `customer`)
- Clean architecture (separated routes, middlewares)
- Docker (PHP, MySQL, NGINX, Node.js)
- ViteJS + TailwindCSS for frontend
- Admin Dashboard with CRUDs (Product, Category, Order, User)
- Full Checkout Flow (Cart → Order + Address)
- Order confirmation email & thank you page
- Profile-based address management
- Product catalog with filters, price range, category and search
- Image upload for products
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
- Shop: `/shop`
- Admin dashboard: `/admin/dashboard`
  - Manage products
  - Manage categories
  - View and update orders
  - Manage users and roles
  - View admin statistics
- Customer:
  - View cart: `/cart`
  - Checkout: `/checkout`
  - Order history: `/my-orders`
  - Address management: `/profile/addresses`

---

## 🧠 Technologies Used

- Laravel 11
- Laravel Breeze
- ViteJS
- TailwindCSS
- Docker (php-fpm, nginx, mysql, node)
- Custom middleware (`isAdmin`)
- Laravel Mailables

---

## 📂 Custom Structure

```
├── docker/
│   ├── nginx.conf
│   └── Dockerfile
├── src/
│   ├── app/
│   │   ├── Models/         # User, Product, Cart, Order, Address
│   │   ├── Http/Controllers/Admin/
│   │   ├── Http/Controllers/Profile/
│   │   ├── Http/Controllers/
│   ├── routes/
│   │   ├── web.php
│   │   ├── admin.php
│   ├── resources/
│   │   ├── views/
│   │   │   ├── admin/
│   │   │   ├── cart/
│   │   │   ├── checkout/
│   │   │   ├── my-orders/
│   │   │   ├── profile/addresses/
│   │   │   ├── shop/
│   └── ...
├── docker-compose.yml
└── README.md
```

---

## ✅ Completed Features

### Admin
- Dashboard with live statistics
- Product CRUD
- Category CRUD
- Order view + status update
- User role management

### Client
- Cart (DB-stored)
- Checkout flow with address selection
- Order creation + storage
- Email confirmation
- Thank you page
- Order history
- Address management with default shipping address
- Product listing in `/shop`
- Category filters, price range filter, and keyword search
- Product detail with image upload

---

## 🛠 Coming Soon

- Stripe payment integration
- Order PDF invoice export
- Admin notifications
