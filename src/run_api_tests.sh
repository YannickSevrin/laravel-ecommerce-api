#!/bin/bash

echo "🧪 Running all API tests..."
echo "================================================"

# Authentication tests
echo "🔐 Authentication tests..."
docker-compose exec app php artisan test --testsuite=Feature --filter="AuthTest"

# Public shop tests
echo "🛒 Shop tests (public products and categories)..."
docker-compose exec app php artisan test --testsuite=Feature --filter="ShopTest"

# Cart tests
echo "🛍️ Cart tests..."
docker-compose exec app php artisan test --testsuite=Feature --filter="CartTest"

# Order tests
echo "📦 Order tests..."
docker-compose exec app php artisan test --testsuite=Feature --filter="OrderTest"

# User profile tests
echo "👤 User profile tests..."
docker-compose exec app php artisan test --testsuite=Feature --filter="ProfileTest"

# Address tests
echo "🏠 Address tests..."
docker-compose exec app php artisan test --testsuite=Feature --filter="AddressTest"

# Admin dashboard tests
echo "📊 Admin dashboard tests..."
docker-compose exec app php artisan test --testsuite=Feature --filter="AdminDashboardTest"

# Admin product tests
echo "🔧 Admin product tests..."
docker-compose exec app php artisan test --testsuite=Feature --filter="AdminProductTest"

# Admin category tests
echo "📁 Admin category tests..."
docker-compose exec app php artisan test --testsuite=Feature --filter="AdminCategoryTest"

echo "================================================"
echo "✅ All API tests completed!"
echo "================================================"

# Run all API tests at once
echo "🏃‍♂️ Complete execution of all API tests..."
docker-compose exec app php artisan test tests/Feature/Api/ 