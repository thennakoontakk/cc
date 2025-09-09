# Crafters Corner Project

This repository contains a full-stack web application with a React frontend, Laravel admin panel, and image generation capabilities.

## Project Structure

The project consists of three main components:

- **Frontend**: React application with Firebase authentication
- **Admin Panel**: Laravel backend for user management and administration
- **Final**: Contains image generation notebooks and resources

## Setup Instructions

### Prerequisites

- PHP 8.1 or higher
- Composer (PHP package manager)
- XAMPP (Apache + MySQL + PHP)
- Node.js and npm
- Git

### Clone the Repository

```bash
git clone https://github.com/thennakoontakk/cc.git
cd cc
```

### Backend Setup (Admin Panel)

1. **Navigate to the admin panel directory**:
   ```bash
   cd admin-panel
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Configure environment**:
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

4. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

5. **Create database**:
   - Start XAMPP and ensure MySQL service is running
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create a new database named `admin_panel`
   - Set collation to `utf8mb4_unicode_ci`

6. **Run migrations**:
   ```bash
   php artisan migrate
   ```

7. **Seed database (optional)**:
   ```bash
   php artisan db:seed
   ```

8. **Start Laravel server**:
   ```bash
   php artisan serve
   ```
   The admin panel will be available at `http://localhost:8000`

### Frontend Setup

1. **Navigate to the frontend directory**:
   ```bash
   cd ../frontend
   ```

2. **Install dependencies**:
   ```bash
   npm install
   ```

3. **Configure Firebase**:
   - Update Firebase configuration in `src/firebase/config.js` with your Firebase project details

4. **Start development server**:
   ```bash
   npm run dev
   ```
   The frontend will be available at `http://localhost:5173`

## Running on XAMPP

To run the project using XAMPP:

1. **Place the project in the XAMPP htdocs directory**:
   - Copy or clone the repository to `C:\xampp\htdocs\crafters-corner`

2. **Configure virtual hosts (optional)**:
   - Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
   - Add:
     ```apache
     <VirtualHost *:80>
         DocumentRoot "C:/xampp/htdocs/crafters-corner/admin-panel/public"
         ServerName admin.crafterscorner.local
     </VirtualHost>
     ```
   - Update your hosts file (`C:\Windows\System32\drivers\etc\hosts`):
     ```
     127.0.0.1 admin.crafterscorner.local
     ```

3. **Set up the database** as described in the Backend Setup section

4. **Access the application**:
   - Admin Panel: `http://admin.crafterscorner.local` or `http://localhost/crafters-corner/admin-panel/public`
   - Frontend: Run with npm as described in the Frontend Setup section

## Troubleshooting

- **Database Connection Issues**: Ensure MySQL is running in XAMPP and credentials in `.env` are correct
- **API Connection Errors**: Check that the backend URL in frontend configuration matches your setup
- **CORS Issues**: Verify CORS settings in `admin-panel/config/cors.php`

## License

This project is proprietary and not licensed for public use without permission.