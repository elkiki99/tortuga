# Tortuga E-commerce Application

This is a Laravel and Vue-based e-commerce application for Tortuga.uy, a platform for buying and selling second‑hand clothing.

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-4FC08D?style=for-the-badge&logo=vue-dot-js&logoColor=white)](https://vuejs.org)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)

## Features
- User authentication with email verification
- Product catalog with categories, brands, and images
- Shopping cart and persistent session handling
- Order processing with email notifications
- Admin middleware for protected routes
- Livewire components for dynamic UI (e.g., Novedades)

## Installation
1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/tortuga.git
   cd tortuga
   ```
2. **Install PHP dependencies**
   ```bash
   composer install
   ```
3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```
4. **Configure environment**
   - Copy `.env.example` to `.env`
   - Set database credentials and other required keys
   - Generate app key:
     ```bash
     php artisan key:generate
     ```
5. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```
6. **Build assets**
   ```bash
   npm run dev   # or npm run prod for production assets
   ```
7. **Start the development server**
   ```bash
   php artisan serve
   ```
   The application will be available at `http://localhost:8000`.

---
*Developed by **Bruno Rossani**, Laravel/Vue full‑stack developer based in Uruguay.*