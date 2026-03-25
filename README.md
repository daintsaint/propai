# PropAI

**Intelligent Property Management Platform**

PropAI is a modern property management platform built with Laravel 11, designed to streamline real estate operations using artificial intelligence.

## Features

- 🤖 **AI-Powered Insights** - Automated property valuations and market analysis
- 📊 **Analytics Dashboard** - Real-time portfolio performance tracking
- 🔒 **Enterprise Security** - Advanced encryption and access controls
- 📝 **FAQ System** - Built-in customer support knowledge base

## Tech Stack

- **Backend**: Laravel 11 (PHP 8.3+)
- **Frontend**: Blade Templates + Vite
- **Database**: SQLite (development), MySQL/PostgreSQL (production)
- **Deployment**: Railway

## Installation

### Prerequisites

- PHP 8.3 or higher
- Composer
- Node.js 20.x or higher
- NPM or Yarn

### Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/daintsaint/propai.git
   cd propai
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Create SQLite database**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

7. **Start development server**
   ```bash
   php artisan serve
   ```

8. **For local development with hot reload**
   ```bash
   npm run dev
   ```

## Routes

- **Home**: `/` - Welcome page
- **FAQ**: `/faq` - Frequently asked questions

## Deployment

PropAI is configured for deployment on Railway:

1. Connect your GitHub repository to Railway
2. Railway will automatically detect Laravel and run the build process
3. Add required environment variables in Railway dashboard
4. Deploy!

## Configuration

### Environment Variables

Key variables in `.env`:

```env
APP_NAME=PropAI
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=sqlite
DB_DATABASE=/app/data/database.sqlite
```

## Project Structure

```
propai/
├── app/
│   ├── Http/Controllers/
│   │   ├── Controller.php
│   │   └── FaqController.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   ├── app.php
│   └── providers.php
├── config/
│   ├── app.php
│   └── database.php
├── database/
│   └── migrations/
├── public/
│   └── index.php
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── faq.blade.php
│       └── welcome.blade.php
├── routes/
│   ├── web.php
│   └── console.php
├── composer.json
└── package.json
```

## Scripts

- `npm run dev` - Start Vite development server
- `npm run build` - Build production assets
- `php artisan serve` - Start PHP development server
- `php artisan migrate` - Run database migrations
- `php artisan cache:clear` - Clear application cache

## License

MIT License - see LICENSE file for details.

## Support

For questions and support, contact: support@propai.com
