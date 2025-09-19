# Blog API - Mini Blog Assignment

A complete REST API for a blog system built with PHP 8+ and Laravel, featuring JWT authentication, CRUD operations, search functionality, and comprehensive testing.

## Features

### Core Requirements
-  **User Authentication** - JWT-based registration and login using Laravel Sanctum
-  **Blog Post CRUD** - Create, read, update, delete posts with proper authorization
-  **Public Access** - Anyone can view posts list and individual posts
-  **Database Design** - Proper relationships between users and posts

### Bonus Features
-  **Search Functionality** - Filter posts by title or body content
-  **Pagination** - 10 posts per page with metadata
-  **Comprehensive Error Handling** - Try-catch blocks with proper HTTP status codes
-  **Database Optimization** - Indexes for performance

## Tech Stack

- **Backend**: PHP 8.1+, Laravel 11
- **Database**: MySQL
- **Authentication**: Laravel Sanctum (JWT)
- **Testing**: Postman Collection included

## API Endpoints

### Authentication
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/register` | User registration | No |
| POST | `/api/login` | User login | No |
| POST | `/api/logout` | User logout | Yes |
| GET | `/api/user` | Get current user | Yes |

### Posts (Public)
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/posts` | List all posts (paginated) | No |
| GET | `/api/posts/{id}` | Get single post | No |
| GET | `/api/posts/search?q=term` | Search posts | No |

### Posts (Authenticated)
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/posts` | Create new post | Yes |
| PUT | `/api/posts/{id}` | Update own post | Yes |
| DELETE | `/api/posts/{id}` | Delete own post | Yes |
| GET | `/api/my-posts` | Get user's posts | Yes |

##  Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL
- Laravel CLI (optional)

### Installation Steps

1. **Clone the repository**
```bash
git clone https://github.com/chimake/blog-api.git
cd blog-api
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database configuration**
Update your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_api
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Create database**
```bash
mysql -u your_username -p
CREATE DATABASE blog_api;
exit
```

6. **Run migrations**
```bash
php artisan migrate
```

7. **Seed database (optional)**
```bash
php artisan db:seed --class=BlogSeeder
```

8. **Start the server**
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`


## Testing

### Using Postman
1. Import the `postman-collection.json` file
2. Set up environment with `base_url = http://localhost:8000`
3. Run the authentication flow first to get tokens
4. Test all endpoints using the organized folder structure

### Test Flow
1. **Register** a new user → Token automatically saved
2. **Create posts** → Post IDs saved for testing
3. **Test public endpoints** → No authentication required
4. **Test authenticated endpoints** → Uses saved tokens
5. **Test error scenarios** → Validation and authorization


## Authentication

The API uses **Laravel Sanctum** for JWT token-based authentication:

1. Register or login to receive a token
2. Include token in requests: `Authorization: Bearer YOUR_TOKEN`
3. Tokens are required for creating, updating, deleting posts
4. Users can only modify their own posts

## Search & Pagination

### Search
```bash
GET /api/posts/search?q=laravel
```

### Pagination
```bash
GET /api/posts?page=2
```

Response includes pagination metadata:
```json
{
  "pagination": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 10,
    "total": 25,
    "from": 1,
    "to": 10
  }
}
```

