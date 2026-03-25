<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PropAI - AI Agents for Your Industry')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/" class="nav-logo">
                <span class="logo-icon">🤖</span>
                <span class="logo-text">PropAI</span>
            </a>
            <div class="nav-menu" id="navMenu">
                <a href="/" class="nav-link">Home</a>
                <a href="/#bundles" class="nav-link">Bundles</a>
                <a href="/#features" class="nav-link">Features</a>
                @auth
                    <a href="/dashboard" class="nav-link">Dashboard</a>
                    <a href="/logout" class="nav-btn nav-btn-secondary">Logout</a>
                @else
                    <a href="/login" class="nav-btn nav-btn-secondary">Login</a>
                    <a href="/register" class="nav-btn nav-btn-primary">Get Started</a>
                @endauth
            </div>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3 class="footer-logo">
                    <span class="logo-icon">🤖</span>
                    PropAI
                </h3>
                <p class="footer-tagline">AI Agents for Your Industry - Subscribe & Automate</p>
            </div>
            <div class="footer-section">
                <h4>Product</h4>
                <a href="/#bundles" class="footer-link">Bundles</a>
                <a href="/#features" class="footer-link">Features</a>
                <a href="/pricing" class="footer-link">Pricing</a>
            </div>
            <div class="footer-section">
                <h4>Company</h4>
                <a href="/about" class="footer-link">About</a>
                <a href="/contact" class="footer-link">Contact</a>
                <a href="/blog" class="footer-link">Blog</a>
            </div>
            <div class="footer-section">
                <h4>Legal</h4>
                <a href="/privacy" class="footer-link">Privacy</a>
                <a href="/terms" class="footer-link">Terms</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} PropAI. All rights reserved.</p>
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
