# E-commerce API Documentation

This documentation describes how to use the REST API for the Laravel e-commerce system.

## Base URL
```
http://localhost:8000/api
```

## Authentication

The API uses Laravel Sanctum for authentication. After login, you will receive a token to include in your requests.

### Required headers for protected routes
```
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json
Accept: application/json
```

## Endpoints

### 🔐 Authentication

#### Registration
```http
POST /api/auth/register
```
**Body:**
```json
{
    "name": "User Name",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login
```http
POST /api/auth/login
```
**Body:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

#### Logout
```http
POST /api/auth/logout
```
*Requires authentication*

#### User profile
```http
GET /api/auth/user
```
*Requires authentication*

---

### 🛍️ Shop (Public)

#### Product list
```http
GET /api/products
```
**Optional parameters:**
- `category` - Category ID
- `search` - Text search
- `min_price` - Minimum price
- `max_price` - Maximum price
- `sort_by` - Sort by (name, price, created_at)
- `sort_direction` - Direction (asc, desc)
- `per_page` - Items per page (default: 12)

#### Product details
```http
GET /api/products/{id}
```

#### Category list
```http
GET /api/categories
```

---

### 🛒 Cart (Authenticated)

#### Show cart
```http
GET /api/cart
```

#### Add to cart
```http
POST /api/cart/add/{product_id}
```
**Body:**
```json
{
    "quantity": 2
}
```

#### Update quantity
```http
PUT /api/cart/update/{product_id}
```
**Body:**
```json
{
    "quantity": 3
}
```

#### Remove from cart
```http
DELETE /api/cart/remove/{product_id}
```

#### Clear cart
```http
DELETE /api/cart/clear
```

---

### 📦 Orders (Authenticated)

#### Order list
```http
GET /api/orders
```

#### Order details
```http
GET /api/orders/{id}
```

#### Create order
```http
POST /api/orders
```
**Body (with existing address):**
```json
{
    "address_id": 1
}
```

**Body (with new address):**
```json
{
    "new_address": {
        "address": "123 Example Street",
        "postal_code": "75001",
        "city": "Paris",
        "country": "France"
    }
}
```

---

### 🏠 Addresses (Authenticated)

#### Address list
```http
GET /api/profile/addresses
```

#### Add address
```http
POST /api/profile/addresses
```
**Body:**
```json
{
    "address": "123 Example Street",
    "postal_code": "75001", 
    "city": "Paris",
    "country": "France",
    "type": "shipping"
}
```

#### Update address
```http
PUT /api/profile/addresses/{id}
```

#### Delete address
```http
DELETE /api/profile/addresses/{id}
```

#### Set default address
```http
POST /api/profile/addresses/{id}/default
```

---

### 👤 Profile (Authenticated)

#### Show profile
```http
GET /api/profile
```

#### Update profile
```http
PUT /api/profile
```
**Body:**
```json
{
    "name": "New Name",
    "email": "new@example.com"
}
```

#### Delete account
```http
DELETE /api/profile
```
**Body:**
```json
{
    "password": "current_password"
}
```

---

## 🔧 Administration (Authenticated + Admin)

### Dashboard
```http
GET /api/admin/dashboard
```

### Product Management
```http
GET /api/admin/products
POST /api/admin/products
GET /api/admin/products/{id}
PUT /api/admin/products/{id}
DELETE /api/admin/products/{id}
```

### Category Management
```http
GET /api/admin/categories
POST /api/admin/categories
GET /api/admin/categories/{id}
PUT /api/admin/categories/{id}
DELETE /api/admin/categories/{id}
```

### Order Management
```http
GET /api/admin/orders
GET /api/admin/orders/{id}
PUT /api/admin/orders/{id}
```

### User Management
```http
GET /api/admin/users
GET /api/admin/users/{id}
PUT /api/admin/users/{id}
```

---

## Response Codes

- `200` - Success
- `201` - Created successfully
- `400` - Validation error
- `401` - Unauthenticated
- `403` - Access denied
- `404` - Resource not found
- `422` - Validation error
- `500` - Server error

## Response Format

### Success
```json
{
    "data": {...},
    "message": "Operation successful"
}
```

### Error
```json
{
    "message": "Error description",
    "errors": {...}
}
```

### Pagination
```json
{
    "data": [...],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 12,
        "total": 60
    },
    "links": {
        "next": "http://localhost:8000/api/products?page=2",
        "prev": null
    }
}
``` 