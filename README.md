# Laravel Inventory API

A clean RESTful API designed to manage products, warehouses, and trace inventory stock movements (Stock Ins and Stock Outs) with real-time balance calculations.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 11/12 (PHP)
- **Database**: SQLite (Zero-config default) / MySQL support
- **Architecture**: Eloquent ORM + Custom Inventory Transactions Logic

---

## 🚀 Getting Started

### 1. Prerequisites
Ensure you have PHP 8.2+ and Composer installed.

### 2. Install Dependencies
```bash
composer install
```

### 3. Setup Environment Configuration
Duplicate the template:
```bash
cp .env.example .env
```
Generate the application encryption key:
```bash
php artisan key:generate
```

### 4. Database Setup & Seeding
Create the local SQLite database file and run the migrations along with the mock data seeder:
```bash
touch database/database.sqlite
php artisan migrate --seed
```

### 5. Start Server
Run the local Laravel development server:
```bash
php artisan serve
```
By default, the API will be accessible at `http://127.0.0.1:8000/api`.

---

## 📋 API Documentation

### 📦 Products CRUD

#### 1. List Products
- **Endpoint**: `GET /api/products`
- **Response**: `200 OK`
- **cURL Example**:
  ```bash
  curl -X GET http://127.0.0.1:8000/api/products
  ```

#### 2. Create Product
- **Endpoint**: `POST /api/products`
- **Request Body**:
  ```json
  {
    "name": "Mechanical RGB Keyboard",
    "sku": "KEYBOARD-M-02",
    "description": "Tactile switch keyboard with RGB backlighting",
    "price": 119.99
  }
  ```
- **Response**: `201 Created`
- **cURL Example**:
  ```bash
  curl -X POST http://127.0.0.1:8000/api/products \
    -H "Content-Type: application/json" \
    -d '{"name": "Mechanical RGB Keyboard", "sku": "KEYBOARD-M-02", "price": 119.99}'
  ```

#### 3. Get Product Detail
- **Endpoint**: `GET /api/products/{id}`
- **Response**: `200 OK` / `404 Not Found`

#### 4. Update Product
- **Endpoint**: `PUT /api/products/{id}`
- **Request Body**: (Allows updating individual fields)
  ```json
  {
    "price": 109.99
  }
  ```
- **Response**: `200 OK`

#### 5. Delete Product
- **Endpoint**: `DELETE /api/products/{id}`
- **Response**: `200 OK`

---

### 🏢 Warehouses CRUD

#### 1. List Warehouses
- **Endpoint**: `GET /api/warehouses`
- **Response**: `200 OK`

#### 2. Create Warehouse
- **Endpoint**: `POST /api/warehouses`
- **Request Body**:
  ```json
  {
    "name": "East Coast Fulfillment",
    "location": "Boston, MA"
  }
  ```
- **Response**: `201 Created`

---

### 📈 Stock Movements & Balances

#### 1. Stock In (Add stock to a warehouse)
- **Endpoint**: `POST /api/stock/in`
- **Request Body**:
  ```json
  {
    "product_id": 1,
    "warehouse_id": 1,
    "quantity": 100,
    "reference": "PO-1001"
  }
  ```
- **Response**: `201 Created`
- **cURL Example**:
  ```bash
  curl -X POST http://127.0.0.1:8000/api/stock/in \
    -H "Content-Type: application/json" \
    -d '{"product_id": 1, "warehouse_id": 1, "quantity": 100, "reference": "PO-1001"}'
  ```

#### 2. Stock Out (Deduct stock from a warehouse)
- **Endpoint**: `POST /api/stock/out`
- **Request Body**:
  ```json
  {
    "product_id": 1,
    "warehouse_id": 1,
    "quantity": 30,
    "reference": "SO-2002"
  }
  ```
- **Response**: `201 Created` (Success) OR `422 Unprocessable Entity` (If stock is insufficient, preventing negative balances).
- **cURL Example**:
  ```bash
  curl -X POST http://127.0.0.1:8000/api/stock/out \
    -H "Content-Type: application/json" \
    -d '{"product_id": 1, "warehouse_id": 1, "quantity": 30, "reference": "SO-2002"}'
  ```

#### 3. Get Product Stock in a Specific Warehouse
- **Endpoint**: `GET /api/stock/product/{productId}/warehouse/{warehouseId}`
- **Response**: `200 OK`
  ```json
  {
    "product": {
      "id": 1,
      "name": "Wireless Ergonomic Mouse",
      "sku": "MOUSE-W-01"
    },
    "warehouse": {
      "id": 1,
      "name": "Main Warehouse"
    },
    "current_stock": 120
  }
  ```
- **cURL Example**:
  ```bash
  curl -X GET http://127.0.0.1:8000/api/stock/product/1/warehouse/1
  ```

#### 4. Get Product Stock Breakdown across all Warehouses
- **Endpoint**: `GET /api/stock/product/{productId}`
- **Response**: `200 OK`
  ```json
  {
    "product": {
      "id": 1,
      "name": "Wireless Ergonomic Mouse",
      "sku": "MOUSE-W-01"
    },
    "total_stock": 170,
    "warehouses": [
      {
        "warehouse_id": 1,
        "warehouse_name": "Main Warehouse",
        "current_stock": 120
      },
      {
        "warehouse_id": 2,
        "warehouse_name": "West Coast Hub",
        "current_stock": 50
      }
    ]
  }
  ```
- **cURL Example**:
  ```bash
  curl -X GET http://127.0.0.1:8000/api/stock/product/1
  ```

---

## 🧪 Running Tests
Run the feature tests to verify API endpoints:
```bash
php artisan test
```
