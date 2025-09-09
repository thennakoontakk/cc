# Laravel Admin Panel

A comprehensive admin panel built with Laravel for managing users authenticated through Firebase. This backend API provides complete user management functionality with role-based access control.

## Features

- **Admin Authentication**: Secure login system for administrators
- **User Management**: Complete CRUD operations for Firebase users
- **Role-Based Access**: Super Admin, Admin, and Moderator roles
- **API Endpoints**: RESTful API for frontend integration
- **Database Integration**: MySQL database with XAMPP support
- **Firebase Integration**: Verify and manage Firebase authenticated users

## Prerequisites

- PHP 8.1 or higher
- Composer (PHP package manager)
- XAMPP (Apache + MySQL + PHP)
- Node.js (for frontend assets, optional)

## Installation & Setup

### 1. XAMPP Configuration

1. **Download and Install XAMPP**:
   - Download from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Install and start Apache and MySQL services

2. **Create Database**:
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create a new database named `admin_panel`
   - Set collation to `utf8mb4_unicode_ci`

### 2. Laravel Setup

1. **Install Dependencies**:
   ```bash
   composer install
   ```

2. **Environment Configuration**:
   - Copy `.env.example` to `.env`
   - Update database credentials:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=admin_panel
     DB_USERNAME=root
     DB_PASSWORD=
     ```

3. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

4. **Run Database Migrations**:
   ```bash
   php artisan migrate
   ```

5. **Seed Database**:
   ```bash
   php artisan db:seed
   ```

6. **Start Development Server**:
   ```bash
   php artisan serve
   ```

### 3. Firebase Configuration

Update the Firebase credentials in your `.env` file:

```env
FIREBASE_PROJECT_ID=your-firebase-project-id
FIREBASE_PRIVATE_KEY_ID=your-private-key-id
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nYour Firebase Private Key Here\n-----END PRIVATE KEY-----\n"
FIREBASE_CLIENT_EMAIL=firebase-adminsdk-xxxxx@your-project.iam.gserviceaccount.com
```

## Project Structure

```
admin-panel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AdminAuthController.php
│   │   │       ├── AdminController.php
│   │   │       └── UserController.php
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php
│   │   │   └── SuperAdminMiddleware.php
│   │   └── Kernel.php
│   └── Models/
│       ├── Admin.php
│       └── User.php
├── config/
│   ├── app.php
│   ├── auth.php
│   └── database.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_users_table.php
│   │   └── 2024_01_01_000002_create_admins_table.php
│   └── seeders/
│       ├── AdminSeeder.php
│       └── DatabaseSeeder.php
├── routes/
│   ├── api.php
│   └── web.php
├── .env
├── .env.example
├── composer.json
└── README.md
```

## API Endpoints

### Authentication
- `POST /api/admin/login` - Admin login
- `POST /api/admin/register` - Register new admin (super admin only)
- `POST /api/admin/logout` - Admin logout
- `GET /api/admin/me` - Get current admin info

### User Management
- `GET /api/admin/users` - List all users (with filters)
- `GET /api/admin/users/{id}` - Get specific user
- `PUT /api/admin/users/{id}` - Update user
- `DELETE /api/admin/users/{id}` - Delete user
- `PUT /api/admin/users/{id}/toggle-status` - Toggle user status
- `POST /api/admin/users/bulk-action` - Bulk actions on users

### Admin Management (Super Admin Only)
- `GET /api/admin/admins` - List all admins
- `POST /api/admin/admins` - Create new admin
- `GET /api/admin/admins/{id}` - Get specific admin
- `PUT /api/admin/admins/{id}` - Update admin
- `DELETE /api/admin/admins/{id}` - Delete admin
- `PUT /api/admin/admins/{id}/toggle-status` - Toggle admin status
- `PUT /api/admin/admins/{id}/change-role` - Change admin role

### Dashboard
- `GET /api/admin/dashboard/stats` - Get dashboard statistics

### Firebase Integration
- `POST /api/verify-firebase-user` - Verify and sync Firebase user

## Default Admin Credentials

After running the seeder, you can login with:

- **Super Admin**: admin@admin.com / password123
- **Admin**: admin@example.com / password123
- **Moderator**: moderator@example.com / password123

## Admin Roles

1. **Super Admin**: Full access to all features including admin management
2. **Admin**: Can manage users but cannot manage other admins
3. **Moderator**: Limited access to user management features

## Security Features

- **Role-based Access Control**: Different permission levels for admin roles
- **API Token Authentication**: Secure API access using Laravel Sanctum
- **Input Validation**: Comprehensive request validation
- **Password Hashing**: Secure password storage
- **CSRF Protection**: Cross-site request forgery protection
- **Rate Limiting**: API rate limiting to prevent abuse

## Database Schema

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - User's email address
- `firebase_uid` - Firebase user ID
- `provider` - Authentication provider (email, google, facebook)
- `is_active` - User status
- `email_verified_at` - Email verification timestamp
- `last_login_at` - Last login timestamp

### Admins Table
- `id` - Primary key
- `name` - Admin's full name
- `email` - Admin's email address
- `password` - Hashed password
- `role` - Admin role (super_admin, admin, moderator)
- `is_active` - Admin status
- `last_login_at` - Last login timestamp

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Ensure XAMPP MySQL is running
   - Check database credentials in `.env`
   - Verify database exists in phpMyAdmin

2. **Permission Denied**:
   - Check file permissions on storage and bootstrap/cache directories
   - Run: `chmod -R 775 storage bootstrap/cache`

3. **Composer Dependencies**:
   - Run: `composer install --no-dev --optimize-autoloader`

4. **Application Key Missing**:
   - Run: `php artisan key:generate`

## Next Steps

1. **Frontend Integration**: Connect with React frontend
2. **Email Notifications**: Set up email notifications for user actions
3. **File Upload**: Add profile picture upload functionality
4. **Activity Logging**: Implement user activity tracking
5. **Advanced Filtering**: Add more filtering options for user management
6. **Export Features**: Add CSV/Excel export for user data
7. **Dashboard Analytics**: Implement detailed analytics and charts

## Support

For issues and questions, please check the Laravel documentation or create an issue in the project repository.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).