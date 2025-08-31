# Project Context for Horario

## Project Overview

This is a **Laravel/Vue.js** application for Horario, a scheduling and booking platform designed for Telegram Mini Apps. The application allows users to register as either service providers (businesses) or customers, with role-based dashboards and functionality.

The application is built with:
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 (Composition API with TypeScript)
- **UI Framework**: Tailwind CSS with shadcn-vue components
- **Deployment**: Designed for Telegram Mini Apps with Inertia.js for SPA navigation

## Key Features

1. **Telegram Mini App Integration**: Full integration with Telegram's WebApp API
2. **Role-based System**: Users can register as either "business" (service providers) or "customer"
3. **Onboarding Flow**: Interactive step-by-step onboarding process based on user role
4. **Service Management**: Businesses can create, edit, and manage services
5. **Booking System**: Customers can browse services and book appointments
6. **Dashboard Views**: Role-specific dashboards with relevant metrics and information
7. **Multi-language Support**: Internationalization support with language switching

## Project Structure

```
├── app/                 # Laravel backend code
│   ├── Http/           # Controllers, middleware, requests
│   ├── Models/         # Eloquent models
│   └── Services/       # Business logic services
├── resources/
│   ├── js/             # Vue.js frontend code
│   │   ├── components/ # Vue components (shadcn-vue UI components)
│   │   ├── composables/ # Vue composables for reusable logic
│   │   ├── layouts/    # Page layouts
│   │   ├── pages/      # Inertia.js pages
│   │   ├── services/   # API service definitions
│   │   └── telegram/   # Telegram integration code
│   └── views/          # Laravel Blade views (minimal)
├── routes/             # Laravel route definitions
├── database/           # Database migrations and seeders
└── tests/             # PestPHP test files
```

## Development Environment

### Prerequisites
- PHP 8.2+
- Node.js 18+
- Composer
- NPM or Yarn

### Setup Commands
1. Install PHP dependencies:
   ```bash
   composer install
   ```
2. Install JavaScript dependencies:
   ```bash
   npm install
   ```
3. Copy and configure environment file:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Set up database:
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

### Development Commands
- **Start development server**: `composer dev` (runs Laravel, queue, logs, and Vite)
- **Build for production**: `npm run build`
- **Lint code**: `npm run lint`
- **Format code**: `npm run format`

### Key Scripts in package.json
- `dev`: Start Vite development server
- `build`: Build frontend assets for production
- `lint`: Run ESLint with auto-fix
- `format`: Format code with Prettier

## Application Flow

1. **Welcome Screen**: Initial app introduction
2. **Role Selection**: User chooses to register as "business" or "customer"
3. **Information Collection**: Role-specific data collection
4. **Business Address**: For business users only
5. **Dashboard Access**: Role-based dashboard with relevant features

## Key Components

### Frontend (Vue.js)
- **Telegram Integration**: Special handling for Telegram WebApp environment
- **Inertia.js**: SPA navigation with Laravel backend
- **shadcn-vue**: UI component library built on Tailwind CSS
- **TypeScript**: Strong typing throughout the frontend

### Backend (Laravel)
- **Inertia Integration**: Server-side rendering and SPA navigation
- **Role-based Middleware**: Access control based on user roles
- **Meta Data**: Extended user model with metadata storage
- **API Controllers**: RESTful controllers for various features

## Testing

- **Backend Testing**: PestPHP for unit and feature tests
- **Frontend Testing**: Currently not configured but can be added

## Deployment Considerations

- Designed for **Telegram Mini Apps** deployment
- Uses **Inertia.js** for hybrid SSR/SPA approach
- **SQLite** database by default (can be changed)
- **Vite** for asset bundling and hot reloading

## Key Routes

- `/` - Welcome screen and onboarding flow
- `/dashboard` - Role-based dashboard redirect
- `/business/dashboard` - Business-specific dashboard
- `/customer/dashboard` - Customer-specific dashboard
- `/services` - Business service management
- `/browse/services` - Customer service browsing
- `/bookings` - Booking management (role-specific)
- `/search` - Service search functionality

## Important Files

- `resources/js/app.ts` - Main frontend entry point
- `routes/web.php` - Main route definitions
- `vite.config.ts` - Frontend build configuration
- `composer.json` - PHP dependencies and scripts
- `package.json` - JavaScript dependencies and scripts