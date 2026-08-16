# Tortuga E-commerce Application

A Laravel and Livewire e-commerce application for Tortuga.uy, a platform for buying and selling second‑hand clothing.

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![SQLite](https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white)](https://www.sqlite.org)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-38B2AC?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)

## Features
- User authentication with email verification
- Product catalog with categories, brands, and images
- Shopping cart and persistent order management
- Mercado Pago payment integration
- Responsive UI with Tailwind CSS and Livewire

## Installation

1. Clone the repository: `git clone https://github.com/elkiki99/tortuga.git`
2. Install dependencies: `composer install && npm install`
3. Copy the environment file: `cp .env.example .env`
4. Generate the application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Start the development server: `php artisan serve`
