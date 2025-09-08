<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Arjuna Advertising API

Welcome to the API documentation for the Arjuna Advertising backend system. 

### Base URL
`http://your-domain.com/api`

---

## Authentication

The API uses Laravel Sanctum for authentication. To access protected endpoints, you need to obtain a bearer token by using the login endpoint and then include it in the `Authorization` header of your requests as a `Bearer Token`.

### Login

- **Endpoint**: `POST /login`
- **Description**: Authenticates a user and returns a bearer token.

**Request Body**:
```json
{
    "email": "admin@example.com",
    "password": "password"
}
```

**Success Response (200 OK)**:
```json
{
    "token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ...",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com",
        "email_verified_at": null,
        "created_at": "2025-09-08T12:00:00.000000Z",
        "updated_at": "2025-09-08T12:00:00.000000Z"
    }
}
```

### Logout

- **Endpoint**: `POST /logout`
- **Authentication**: Required.
- **Description**: Revokes the user's current access token.

**Success Response (200 OK)**:
```json
{
    "message": "Logged out successfully"
}
```

### Unauthenticated Error

If you access a protected endpoint without a valid token, you will receive the following response.

**Error Response (401 Unauthorized)**:
```json
{
    "message": "Anda tidak berhak mengakses resource ini. Silakan login terlebih dahulu."
}
```

---

## Roles & Permissions

The API has two user roles with different access levels:

-   `admin`: Can access all API endpoints.
-   `karyawan`: Can only access the `Invoice` and `Inventory` endpoints.

---

## Endpoints

### Invoices

- **Endpoint**: `/invoice`
- **Required Role**: `admin` or `karyawan`

| Method | URI                  | Description              |
|--------|----------------------|--------------------------|
| `GET`  | `/invoice`           | Get a list of all invoices. |
| `GET`  | `/invoice/{id}`      | Get a single invoice.    |
| `POST` | `/invoice`           | Create a new invoice.    |
| `PUT`  | `/invoice/{id}`      | Update an invoice.       |
| `DELETE`| `/invoice/{id}`     | Delete an invoice.       |

**Create/Update Invoice (`POST` or `PUT`)**

*Request Body*:
```json
{
    "customer_name": "PT. Pelanggan Sejahtera",
    "source_id": 1,
    "due_date": "2025-12-31",
    "status_id": 1,
    "discount": 5000,
    "tax_enabled": false,
    "items": [
        {
            "inventory_id": 1,
            "quantity": 10
        },
        {
            "inventory_id": 2,
            "quantity": 5
        }
    ]
}
```

*Success Response (201 Created or 200 OK)*:
```json
{
    "status": "success",
    "data": {
        "customer_name": "PT. Pelanggan Sejahtera",
        "source_id": 1,
        "due_date": "2025-12-31",
        "status_id": 1,
        // ... other fields ...
        "items": [
            {
                "id": 1,
                "invoice_id": 1,
                "inventory_id": 1,
                "quantity": 10,
                "price": "15000.00",
                "sub_total": "150000.00"
            }
        ]
    },
    "message": "Invoice created/updated successfully"
}
```

### Inventory

- **Endpoint**: `/inventory`
- **Required Role**: `admin` or `karyawan`

| Method | URI                  | Description              |
|--------|----------------------|--------------------------|
| `GET`  | `/inventory`         | Get a list of all inventory items. |
| `GET`  | `/inventory/{id}`    | Get a single inventory item.    |
| `POST` | `/inventory`         | Create a new inventory item.    |
| `PUT`  | `/inventory/{id}`    | Update an inventory item.       |
| `DELETE`| `/inventory/{id}`   | Delete an inventory item.       |

**Create/Update Inventory (`POST` or `PUT`)**

*Request Body*:
```json
{
    "product_name": "Spanduk Flexi 280g",
    "type": "Digital Print",
    "quality": "Standard",
    "unit_id": 1,
    "stock": 100,
    "price": 25000
}
```

*Success Response (201 Created or 200 OK)*:
```json
{
    "status": "success",
    "data": {
        "id": 1,
        "kode_inventory": "ITM-001",
        "product_name": "Spanduk Flexi 280g",
        "type": "Digital Print",
        "quality": "Standard",
        "unit_id": 1,
        "stock": 100,
        "price": "25000.00"
    },
    "message": "Inventory item created/updated successfully"
}
```

### Users

- **Endpoint**: `/user`
- **Required Role**: `admin`

| Method | URI                  | Description              |
|--------|----------------------|--------------------------|
| `GET`  | `/user`              | Get a list of all users. |
| `GET`  | `/user/{id}`         | Get a single user.    |
| `POST` | `/user`              | Create a new user.    |
| `PUT`  | `/user/{id}`         | Update a user.       |
| `DELETE`| `/user/{id}`         | Delete a user.       |

**Create/Update User (`POST` or `PUT`)**

*Request Body*:
```json
{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "a-strong-password",
    "password_confirmation": "a-strong-password"
}
```

*Success Response (201 Created or 200 OK)*:
```json
{
    "status": "success",
    "data": {
        "id": 2,
        "name": "John Doe",
        "email": "john.doe@example.com"
    },
    "message": "User created/updated successfully"
}
```

### Other Endpoints (Admin Only)

These endpoints follow the standard RESTful resource structure. The request body for `POST` and `PUT` typically involves a `name` field.

- **Units**: `GET`, `POST`, `PUT`, `DELETE` on `/api/unit`
- **Sumber Pelanggan**: `GET`, `POST`, `PUT`, `DELETE` on `/api/sumber-pelanggan`

**Example Request (`POST /api/unit`)**
```json
{
    "name": "Meter"
}
```

**Example Response (`GET /api/unit/1`)**
```json
{
    "status": "success",
    "data": {
        "id": 1,
        "name": "Meter"
    },
    "message": "Unit retrieved successfully"
}
```

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).