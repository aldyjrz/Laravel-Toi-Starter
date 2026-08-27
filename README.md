# Laravel Toi

A Laravel 12 starter kit with authentication, admin dashboard, Tailwind CSS, and Lucide Icons.

**Package:** `aldytoi/laravel-toi`

---

## Requirements

- PHP >= 8.2
- Laravel 12.x
- Composer 2.x

---

## Installation

### 1. Create a new Laravel project

```bash
composer create-project laravel/laravel:^12.0 my-app
cd my-app
```

### 2. Require the package

```bash
composer require aldytoi/laravel-toi
```

### 3. Run the installer

```bash
php artisan toi:install
```

The installer will:

- Verify Laravel 12 compatibility
- Install configuration
- Install User model
- Install User migration
- Install authentication controllers
- Install routes
- Install Blade layouts and views
- Install admin dashboard
- Install Tailwind CSS and Vite resources

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Install and build frontend assets

```bash
npm install
npm run build
```

---

## Authentication

Laravel Toi provides session-based authentication out of the box.

### Routes

| Method | URI          | Description      |
|--------|-------------|------------------|
| GET    | /login      | Login page       |
| POST   | /login      | Login action     |
| GET    | /register   | Registration page|
| POST   | /register   | Register action  |
| POST   | /logout     | Logout action    |
| GET    | /admin      | Admin dashboard  |

### Features

- User registration with validation
- Login with email and password
- Remember me functionality
- Session regeneration on login
- CSRF protection
- Password hashing (via model cast)
- Protected admin dashboard

---

## Tailwind CSS

Laravel Toi uses Tailwind CSS for all default UI. The package installs Tailwind configuration appropriate for Laravel 12.

```bash
npm install
npm run dev    # Development server
npm run build  # Production build
```

---

## Lucide Icons

All default UI uses [Lucide Icons](https://lucide.dev) rendered as inline SVGs.

### Usage in Blade

```blade
@lucide('mail')
@lucide('lock')
@lucide('eye')
```

### Available Icons

The package includes the following Lucide icons:

- `mail`, `lock`, `eye`, `eye-off`, `log-in`
- `user`, `user-plus`, `user-check`, `user-circle`
- `layout-dashboard`, `users`, `settings`
- `log-out`, `search`, `bell`, `menu`
- `chevron-down`, `check-circle`

---

## Admin Dashboard

The admin dashboard is accessible at `/admin` and requires authentication.

Features:

- Responsive sidebar navigation
- Mobile-friendly drawer menu
- Top navigation with search and notifications
- User profile menu
- Statistics cards (mock data)
- Recent activity section

---

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=toi-config
```

Available options in `config/toi.php`:

| Option          | Description                          | Default    |
|----------------|--------------------------------------|------------|
| `name`         | Application name                     | `Laravel`  |
| `logo`         | Logo URL for authentication views    | `null`     |
| `dashboard_uri`| URI for the admin dashboard          | `admin`    |
| `user_model`   | User model class                     | `App\Models\User` |

---

## Customization

### Views

All views are standard Blade templates using Tailwind CSS. You can customize them by editing the files in `resources/views/`.

Published views:

- `resources/views/layouts/app.blade.php` - Main layout
- `resources/views/layouts/auth.blade.php` - Auth layout
- `resources/views/auth/login.blade.php` - Login page
- `resources/views/auth/register.blade.php` - Register page
- `resources/views/dashboard/index.blade.php` - Dashboard

### Models

The User model at `app/Models/User.php` follows Laravel conventions and can be extended as needed.

### Controllers

Authentication controllers are in `app/Http/Controllers/Auth/`:

- `LoginController.php`
- `RegisterController.php`
- `LogoutController.php`

---

## Testing

```bash
php artisan test
```

Or with Pest:

```bash
vendor/bin/pest
```

---

## Troubleshooting

### "Laravel Toi requires Laravel 12"

Ensure you are running Laravel 12 or higher. Check with:

```bash
php artisan --version
```

### Files already exist

The installer skips existing files by default. Use `--force` to overwrite:

```bash
php artisan toi:install --force
```

### Migration not found

Run migrations manually:

```bash
php artisan migrate
```

### Assets not compiling

Ensure Node.js and npm are installed, then:

```bash
npm install
npm run build
```

---

## Development

### Local development

```bash
git clone https://github.com/aldytoi/laravel-toi.git
cd laravel-toi
composer install
```

### Running tests

```bash
vendor/bin/phpunit
```

---

## License

MIT License. See [LICENSE](LICENSE) for details.
