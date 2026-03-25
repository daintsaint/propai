# PropAI Deployment Guide

Complete deployment guide for PropAI - AI Agent as a Service platform on Railway.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Quick Start](#quick-start)
3. [Railway Deployment](#railway-deployment)
4. [Environment Configuration](#environment-configuration)
5. [Database Setup](#database-setup)
6. [Stripe Integration](#stripe-integration)
7. [Post-Deployment Steps](#post-deployment-steps)
8. [Troubleshooting](#troubleshooting)

---

## Prerequisites

Before deploying, ensure you have:

- [ ] GitHub account with the `daintsaint/propai` repository
- [ ] Railway account (https://railway.app)
- [ ] Stripe account for payment processing
- [ ] Domain name (optional, for custom URL)
- [ ] Telegram Bot Token (for agent integrations)

---

## Quick Start

### 1-Click Deploy to Railway

1. Visit [Railway](https://railway.app)
2. Click "New Project"
3. Select "Deploy from GitHub repo"
4. Choose `daintsaint/propai` repository
5. Railway will auto-detect the Laravel/PHP configuration
6. Click "Deploy"

Railway will automatically:
- Install PHP 8.3+ and Composer
- Install Node.js and npm dependencies
- Build frontend assets
- Run database migrations
- Start the application

---

## Railway Deployment

### Build Configuration

The project includes:
- `railway.toml` - Railway build and deploy configuration
- `build.sh` - Build script for dependencies and migrations
- `Procfile` - Web server startup command

Railway uses Railpack to auto-detect Laravel and will:
1. Install Composer dependencies
2. Build Vite assets (npm install && npm run build)
3. Cache configuration and routes
4. Run migrations
5. Start the PHP development server

### Environment Variables

Set the following in Railway Dashboard > Project > Variables:

#### Required Variables

```bash
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.railway.app
APP_KEY=base64:generate_with_artisan

# Database (SQLite on Railway)
DB_CONNECTION=sqlite
DB_DATABASE=/app/data/database.sqlite

# Session
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIES=false
```

#### Stripe Configuration

```bash
STRIPE_KEY=pk_test_your_public_key
STRIPE_SECRET=sk_test_your_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

#### Optional Integrations

```bash
# Telegram (for agent integrations)
TELEGRAM_BOT_TOKEN=your_bot_token

# Email (Mailgun/Postmark/Resend)
MAIL_MAILER=log  # Change to mailgun/postmark/resend in production

# Error Tracking
SENTRY_LARAVEL_DSN=your_sentry_dsn
```

---

## Environment Configuration

### Generate Application Key

Run locally or in Railway shell:

```bash
php artisan key:generate
```

Copy the generated key to Railway variables as `APP_KEY`.

### Production Settings

The `.env.example` file includes production-ready defaults:

- `APP_DEBUG=false` - Disable debug mode
- `LOG_CHANNEL=errorlog` - Log to stdout/stderr
- `SESSION_DRIVER=cookie` - Use cookie sessions
- `DB_CONNECTION=sqlite` - Use SQLite database

---

## Database Setup

### SQLite (Default)

Railway provides persistent storage at `/app/data/`. The SQLite database is automatically created at:

```
/app/data/database.sqlite
```

Migrations run automatically on deployment via `build.sh`.

### MySQL/PostgreSQL (Optional)

For production workloads, consider Railway's managed MySQL or PostgreSQL:

1. Add MySQL plugin in Railway Dashboard
2. Set environment variables:

```bash
DB_CONNECTION=mysql
DB_HOST=${{RAILWAY_MYSQL_HOST}}
DB_PORT=3306
DB_DATABASE=${{RAILWAY_MYSQL_DATABASE}}
DB_USERNAME=${{RAILWAY_MYSQL_USER}}
DB_PASSWORD=${{RAILWAY_MYSQL_PASSWORD}}
```

---

## Stripe Integration

### Setup Stripe Products

1. Log into [Stripe Dashboard](https://dashboard.stripe.com)
2. Create products for each vertical bundle:
   - Real Estate Agent ($49/mo)
   - E-commerce Store ($79/mo)
   - Local Business ($39/mo)
   - Professional Services ($99/mo)
   - SaaS Startup ($149/mo)

3. Create recurring prices for each product
4. Copy Product IDs and Price IDs to Railway variables:

```bash
STRIPE_PRODUCT_ID_BASIC=prod_xxx
STRIPE_PRICE_ID_BASIC=price_xxx
STRIPE_PRODUCT_ID_PRO=prod_yyy
STRIPE_PRICE_ID_PRO=price_yyy
```

### Configure Webhooks

1. In Stripe Dashboard > Developers > Webhooks
2. Add endpoint: `https://your-app.railway.app/api/webhooks/stripe`
3. Select events:
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.payment.succeeded`
   - `invoice.payment.failed`
4. Copy webhook secret to `STRIPE_WEBHOOK_SECRET`

---

## Post-Deployment Steps

### 1. Verify Health Check

Visit `https://your-app.railway.app/` to confirm the app is running.

### 2. Run Database Seeder

In Railway Shell:

```bash
php artisan db:seed --class=VerticalBundleSeeder
```

This seeds the 5 vertical bundles into the database.

### 3. Test Registration

Visit `/register` and create a test account.

### 4. Verify API Endpoints

Test key endpoints:

```bash
# Health check
GET https://your-app.railway.app/api/health

# List bundles
GET https://your-app.railway.app/api/verticals

# Dashboard (requires auth)
GET https://your-app.railway.app/api/dashboard
```

### 5. Configure Custom Domain (Optional)

1. Railway Dashboard > Settings > Domains
2. Add your domain
3. Update DNS records
4. Update `APP_URL` environment variable

---

## Troubleshooting

### Common Issues

#### 1. Application won't start

**Symptom:** Railway shows "Crash loop" or "Exited with code 1"

**Solution:**
- Check logs in Railway Dashboard
- Verify `APP_KEY` is set
- Ensure `/app/data` directory is writable
- Check build logs for failed dependencies

#### 2. Database migrations fail

**Symptom:** "Database file not found" or migration errors

**Solution:**
```bash
# In Railway Shell
mkdir -p /app/data
touch /app/data/database.sqlite
php artisan migrate --force
```

#### 3. Stripe webhooks not working

**Symptom:** Payments succeed but subscriptions don't activate

**Solution:**
- Verify `STRIPE_WEBHOOK_SECRET` matches Stripe dashboard
- Check webhook endpoint is publicly accessible
- Review logs for webhook signature verification errors

#### 4. Assets not loading

**Symptom:** CSS/JS files return 404

**Solution:**
```bash
# Rebuild assets
npm install
npm run build
php artisan view:clear
```

#### 5. Session issues

**Symptom:** Users can't stay logged in

**Solution:**
- Set `SESSION_SECURE_COOKIES=false` for HTTP
- Ensure `SESSION_DRIVER` is set correctly
- Check cookie domain settings

### View Logs

In Railway Dashboard:
1. Click on your deployment
2. Select "Deployments" tab
3. Click on the latest deployment
4. View real-time logs

Or use Railway CLI:

```bash
railway logs
```

### Access Railway Shell

```bash
railway shell
```

Then run artisan commands:

```bash
php artisan tinker
php artisan cache:clear
php artisan config:clear
php artisan route:list
```

---

## Monitoring & Maintenance

### Health Checks

Railway automatically health checks the `/` endpoint every 60 seconds.

### Automatic Restarts

Configure automatic restarts in `railway.toml`:

```toml
[deploy]
restartPolicyType = "ON_FAILURE"
restartPolicyMaxRetries = 3
```

### Backup Database

For SQLite:

```bash
# Download database file
rsync -avz your-app.railway.app:/app/data/database.sqlite ./backup.sqlite
```

For managed databases, use Railway's built-in backups.

### Update Deployment

1. Push changes to GitHub main branch
2. Railway automatically redeploys
3. Monitor deployment logs

---

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] `APP_KEY` is unique and secret
- [ ] Stripe keys are production keys (not test keys)
- [ ] Database is backed up regularly
- [ ] HTTPS is enabled (automatic on Railway)
- [ ] CORS is configured for your domain
- [ ] Rate limiting is enabled
- [ ] Error tracking (Sentry) is configured

---

## Support

For issues:
1. Check Railway status: https://status.railway.app
2. Review Laravel docs: https://laravel.com/docs
3. Contact: desmond@orfeostory.com

---

**Last Updated:** March 25, 2026
**Version:** 1.0.0
