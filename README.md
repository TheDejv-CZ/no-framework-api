# PHP To-Do List API Documentation

## Overview
This API allows you to manage a list of to-do items, including creating, reading, updating, and deleting to-do items.

## Base URL
`http://localhost:8000/api`

## Endpoints
### Create a To-Do Item
**Endpoint:** `POST /todos`

**Description:** Creates a new to-do item.

**Request Body:**
```
{
    "title": "string",
    "description": "string",
    "status": "pending" | "completed"
} 
```

**Example Request:**
```
curl -X POST http://localhost:8000/api/todos -H "Content-Type: application/json" -d '{
  "title": "Title 1",
  "description": "Description 1",
  "status": "pending"
}'
```

**Response:**
```
{
  "id": 1,
  "title": "Buy groceries",
  "description": "Milk, Bread, Butter",
  "status": "pending"
}
```

### Read All To-Do Items
**Endpoint:** `GET /todos`

**Description:** Retrieves a list of all to-do items.

**Example Request:**
```
curl -X GET http://localhost:8000/api/todos'
```

**Response:**
```
[
  {
    "id": 1,
    "title": "Title 1"",
    "description": "Description 1",
    "status": "pending"
  },
  {
    "id": 2,
    "title": "Title 2",
    "description": "Desc 2",
    "status": "completed"
  }
]
```

### Read a Single To-Do Item
**Endpoint:** `GET /todos/{id}`

**Description:** Retrieves a specific to-do item by ID.

**URL Parameters:** 
- `id` (integer): The ID of the to-do item to retrieve.

**Example Request:**
```
curl -X GET http://localhost:8000/api/todos/1
```

**Response:**
```
{
  "id": 1,
  "title": "Buy groceries",
  "description": "Milk, Bread, Butter",
  "status": "pending"
}
```

### Update a To-Do Item
**Endpoint:** `PUT /todos/{id}`

**Description:** Updates an existing to-do item by ID.

**URL Parameters:** 
- `id` (integer): The ID of the to-do item to update.

**Request Body:**
```
{
    "title": "string",
    "description": "string",
    "status": "pending" | "completed"
} 
```

**Example Request:**
```
curl -X PUT http://localhost:8000/api/todos/1 -H "Content-Type: application/json" -d '{
  "title": "Title 1",
  "description": "Some description",
  "status": "completed"
}'
```

**Response:**
```
{
  "id": 1,
  "title": "Title 1",
  "description": "Some description",
  "status": "completed"
}
```

### Delete a To-Do Item
**Endpoint:** `DELETE /todos/{id}`

**Description:** Deletes a specific to-do item by ID.

**URL Parameters:**
- `id` (integer): The ID of the to-do item to delete.

**Example Request:**
```
curl -X DELETE http://localhost:8000/api/todos/1
```

**Response:**
```
{ }
```

## Error Handling

The API returns the following error responses:
- **400 Bad Request:** The request could not be understood by the server due to malformed syntax.
- **404 Not Found:** The requested resource could not be found.
- **500 Internal Server Error:** The server encountered an unexpected condition which prevented it from fulfilling the request.

**Example Error Response:**
```
{
  "error": "New To-do Item has not been saved. Sorry."
}
```

## Setup and Configuration
To set up and run the PHP backend for the to-do list API, follow these steps:
1. **Install Dependencies:** <br>
   ```
   composer install
   ```
2. **Database setup:** <br>
   Create a SQLite database file named database.db and a table named todos with the following schema:
    ```
   CREATE TABLE todos (
   id INTEGER PRIMARY KEY AUTOINCREMENT,
   title TEXT,
   description TEXT,
   status TEXT CHECK( status IN ('pending','completed') )
   );
   ```
3. **Start the PHP Built-in Server:** <br>
    ```
   php -S localhost:8000 -t public
   ```