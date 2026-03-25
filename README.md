# PropAI - AI Agent as a Service Platform

A Laravel 11 SaaS platform for delivering vertical-specific AI agent automations. Users subscribe to bundles tailored to their industry (real estate, e-commerce, local business, etc.) and get access to pre-configured AI agents for lead management, customer communication, and business automation.

## Features

- **Vertical-Specific Bundles**: Pre-packaged AI automation suites for different industries
- **Subscription Management**: Stripe-powered recurring billing with webhook integration
- **User Authentication**: Secure registration, login, and password reset with Laravel Sanctum
- **Agent Instance Management**: Deploy and configure AI agents per user
- **Lead Management**: Built-in CRM for tracking and managing leads
- **Multi-Channel Integration**: Telegram, Email, and SMS automation capabilities

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Database**: SQLite (default), MySQL/PostgreSQL supported
- **Authentication**: Laravel Sanctum (API tokens)
- **Payments**: Stripe Subscriptions
- **Queue**: Database driver (configurable to Redis)
- **Deployment**: Railway-ready

## Quick Start

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM (for frontend assets)
- SQLite, MySQL, or PostgreSQL

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/daintsaint/propai.git
cd propai
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Set up database**
```bash
# For SQLite (default)
touch database/database.sqlite

# Run migrations
php artisan migrate
```

5. **Seed vertical bundles**
```bash
php artisan db:seed
```

6. **Start development server**
```bash
php artisan serve
```

## Directory Structure

```
propai/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Registration, login, logout
│   │   │   ├── DashboardController.php     # User dashboard data
│   │   │   ├── VerticalController.php      # Bundle browsing & activation
│   │   │   └── SubscriptionController.php  # Subscription management & webhooks
│   │   └── Middleware/
│   │       ├── AuthMiddleware.php          # Authentication check
│   │       └── SubscriptionActive.php      # Active subscription check
│   └── Models/
│       ├── User.php                        # User model
│       ├── Subscription.php                # Subscription model
│       ├── VerticalBundle.php              # Bundle model
│       ├── AgentInstance.php               # Agent instance model
│       └── Lead.php                        # Lead model
├── database/
│   ├── migrations/                         # Database migrations
│   └── seeders/
│       └── VerticalBundleSeeder.php        # Seed 5 vertical bundles
├── routes/
│   └── api.php                             # API route definitions
└── config/
    ├── database.php                        # Database configuration
    ├── auth.php                            # Authentication configuration
    └── services.php                        # Third-party services (Stripe, Telegram)
```

## API Endpoints

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register new user |
| POST | `/api/login` | Login user |
| POST | `/api/logout` | Logout user (requires auth) |
| GET | `/api/user` | Get authenticated user |
| POST | `/api/forgot-password` | Send password reset link |
| POST | `/api/reset-password` | Reset password with token |

### Dashboard

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/dashboard` | Get dashboard overview |
| GET | `/api/dashboard/agents` | Get user's agents |
| GET | `/api/dashboard/agents/active` | Get active agents |
| GET | `/api/dashboard/leads` | Get user's leads |
| GET | `/api/dashboard/leads/{status}` | Get leads by status |

### Vertical Bundles

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/verticals` | List all bundles |
| GET | `/api/verticals/{slug}` | Get specific bundle |
| GET | `/api/verticals/current` | Get user's current bundle |
| POST | `/api/verticals/{slug}/activate` | Activate bundle |
| POST | `/api/verticals/{slug}/upgrade` | Upgrade to different bundle |

### Subscriptions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/subscriptions` | Get subscription details |
| POST | `/api/subscriptions/cancel` | Cancel subscription |
| POST | `/api/subscriptions/reactivate` | Reactivate cancelled subscription |
| POST | `/api/webhooks/stripe` | Stripe webhook handler |

## Vertical Bundles

### Available Bundles

1. **Real Estate Agent** - $49/month
   - Automated lead follow-up emails
   - Telegram bot for property inquiries
   - CRM synchronization

2. **E-commerce Store** - $79/month
   - Abandoned cart recovery
   - Order status notifications
   - Customer support automation

3. **Local Business** - $39/month
   - Appointment booking automation
   - Review request campaigns
   - SMS/email notifications

4. **Professional Services** - $99/month
   - Client onboarding automation
   - Document collection workflows
   - Meeting scheduling

5. **SaaS Startup** - $149/month
   - User onboarding sequences
   - Churn prevention workflows
   - Customer health scoring

## Configuration

### Stripe Setup

1. Get your Stripe API keys from [Stripe Dashboard](https://dashboard.stripe.com/apikeys)
2. Set webhook endpoint: `https://your-domain.com/api/webhooks/stripe`
3. Update `.env`:
```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### Telegram Setup

1. Create bot via [@BotFather](https://t.me/botfather)
2. Update `.env`:
```env
TELEGRAM_BOT_TOKEN=your_bot_token
```

## Development

### Running Tests
```bash
php artisan test
```

### Queue Worker (for background jobs)
```bash
php artisan queue:work
```

### Database Seeding
```bash
php artisan db:seed
```

## Deployment

PropAI is configured for deployment on Railway. See `railway.toml` for build configuration.

### Environment Variables for Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=propai
DB_USERNAME=your-username
DB_PASSWORD=your-password

STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

TELEGRAM_BOT_TOKEN=your_production_token
```

## License

MIT License - see [LICENSE](LICENSE) file for details.

## Support

For issues and feature requests, please create an issue on GitHub.
