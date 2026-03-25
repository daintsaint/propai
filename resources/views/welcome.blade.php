@extends('layouts.app')

@section('title', 'PropAI - AI Agents for Your Industry')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <h1 class="hero-title">
                <span class="gradient-text">AI Agents</span> for Your Industry<br>
                <span class="text-muted">Subscribe & Automate</span>
            </h1>
            <p class="hero-subtitle">
                Pre-built AI agents tailored for your specific industry. Set up in minutes, not months.
                Choose your vertical, subscribe, and start automating today.
            </p>
            <div class="hero-cta">
                <a href="/register" class="btn btn-primary btn-lg">Get Started</a>
                <a href="#demo" class="btn btn-outline btn-lg">View Demo</a>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number">5+</span>
                    <span class="stat-label">Industry Bundles</span>
                </div>
                <div class="stat">
                    <span class="stat-number">1-Click</span>
                    <span class="stat-label">Setup</span>
                </div>
                <div class="stat">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Automation</span>
                </div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-image">
                <div class="floating-card card-1">
                    <span class="card-icon">📧</span>
                    <span>Auto-Email</span>
                </div>
                <div class="floating-card card-2">
                    <span class="card-icon">📊</span>
                    <span>Analytics</span>
                </div>
                <div class="floating-card card-3">
                    <span class="card-icon">🤖</span>
                    <span>AI Agent</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bundles Section -->
<section id="bundles" class="bundles-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Choose Your Industry Bundle</h2>
            <p class="section-subtitle">Pre-configured AI agents designed for your specific needs</p>
        </div>
        <div class="bundles-grid" id="bundlesGrid">
            <!-- Real Estate Bundle -->
            <div class="bundle-card" data-bundle="real-estate">
                <div class="bundle-header">
                    <span class="bundle-icon">🏠</span>
                    <span class="bundle-badge">Popular</span>
                </div>
                <h3 class="bundle-title">Real Estate Agent</h3>
                <p class="bundle-description">Automate lead follow-ups, schedule viewings, and manage client communications</p>
                <div class="bundle-price">
                    <span class="price-currency">$</span>
                    <span class="price-amount">49</span>
                    <span class="price-period">/month</span>
                </div>
                <ul class="bundle-features">
                    <li><span class="check">✓</span> Lead Management Agent</li>
                    <li><span class="check">✓</span> Auto Email Follow-ups</li>
                    <li><span class="check">✓</span> Appointment Scheduling</li>
                    <li><span class="check">✓</span> Property Listing Updates</li>
                    <li><span class="check">✓</span> Client CRM Integration</li>
                </ul>
                <a href="/bundles/real-estate" class="btn btn-primary btn-block">View Details</a>
            </div>

            <!-- E-commerce Bundle -->
            <div class="bundle-card" data-bundle="ecommerce">
                <div class="bundle-header">
                    <span class="bundle-icon">🛒</span>
                </div>
                <h3 class="bundle-title">E-commerce Store</h3>
                <p class="bundle-description">Handle customer support, process returns, and boost sales with AI</p>
                <div class="bundle-price">
                    <span class="price-currency">$</span>
                    <span class="price-amount">79</span>
                    <span class="price-period">/month</span>
                </div>
                <ul class="bundle-features">
                    <li><span class="check">✓</span> Customer Support Agent</li>
                    <li><span class="check">✓</span> Order Processing</li>
                    <li><span class="check">✓</span> Return Management</li>
                    <li><span class="check">✓</span> Product Recommendations</li>
                    <li><span class="check">✓</span> Review Monitoring</li>
                </ul>
                <a href="/bundles/ecommerce" class="btn btn-primary btn-block">View Details</a>
            </div>

            <!-- Local Business Bundle -->
            <div class="bundle-card featured" data-bundle="local-business">
                <div class="bundle-header">
                    <span class="bundle-icon">🏪</span>
                    <span class="bundle-badge featured-badge">Best Value</span>
                </div>
                <h3 class="bundle-title">Local Business</h3>
                <p class="bundle-description">Manage appointments, respond to reviews, and engage local customers</p>
                <div class="bundle-price">
                    <span class="price-currency">$</span>
                    <span class="price-amount">39</span>
                    <span class="price-period">/month</span>
                </div>
                <ul class="bundle-features">
                    <li><span class="check">✓</span> Appointment Booking</li>
                    <li><span class="check">✓</span> Review Response Agent</li>
                    <li><span class="check">✓</span> SMS Notifications</li>
                    <li><span class="check">✓</span> Local SEO Updates</li>
                    <li><span class="check">✓</span> Customer Database</li>
                </ul>
                <a href="/bundles/local-business" class="btn btn-primary btn-block">View Details</a>
            </div>

            <!-- Professional Services Bundle -->
            <div class="bundle-card" data-bundle="professional">
                <div class="bundle-header">
                    <span class="bundle-icon">💼</span>
                </div>
                <h3 class="bundle-title">Professional Services</h3>
                <p class="bundle-description">Streamline client onboarding, document management, and billing</p>
                <div class="bundle-price">
                    <span class="price-currency">$</span>
                    <span class="price-amount">99</span>
                    <span class="price-period">/month</span>
                </div>
                <ul class="bundle-features">
                    <li><span class="check">✓</span> Client Onboarding Agent</li>
                    <li><span class="check">✓</span> Document Automation</li>
                    <li><span class="check">✓</span> Invoice Generation</li>
                    <li><span class="check">✓</span> Meeting Scheduler</li>
                    <li><span class="check">✓</span> Time Tracking</li>
                </ul>
                <a href="/bundles/professional" class="btn btn-primary btn-block">View Details</a>
            </div>

            <!-- SaaS Bundle -->
            <div class="bundle-card" data-bundle="saas">
                <div class="bundle-header">
                    <span class="bundle-icon">🚀</span>
                </div>
                <h3 class="bundle-title">SaaS Startup</h3>
                <p class="bundle-description">Automate user onboarding, support tickets, and growth metrics</p>
                <div class="bundle-price">
                    <span class="price-currency">$</span>
                    <span class="price-amount">149</span>
                    <span class="price-period">/month</span>
                </div>
                <ul class="bundle-features">
                    <li><span class="check">✓</span> User Onboarding Flow</li>
                    <li><span class="check">✓</span> Support Ticket Agent</li>
                    <li><span class="check">✓</span> Analytics Dashboard</li>
                    <li><span class="check">✓</span> Churn Prevention</li>
                    <li><span class="check">✓</span> API Integration Hub</li>
                </ul>
                <a href="/bundles/saas" class="btn btn-primary btn-block">View Details</a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="features-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">What You Get</h2>
            <p class="section-subtitle">Everything you need to automate your business operations</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h3 class="feature-title">Pre-built AI Agents</h3>
                <p class="feature-description">Industry-specific agents ready to deploy. No training required, no complex setup.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3 class="feature-title">One-Click Setup</h3>
                <p class="feature-description">Activate your bundle in seconds. Connect your tools and start automating immediately.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💳</div>
                <h3 class="feature-title">Monthly Subscription</h3>
                <p class="feature-description">Flexible pricing with no long-term commitments. Cancel anytime, no questions asked.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔧</div>
                <h3 class="feature-title">Industry-Tuned</h3>
                <p class="feature-description">Agents trained on your industry's best practices, terminology, and workflows.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3 class="feature-title">Multi-Channel</h3>
                <p class="feature-description">Email, SMS, Telegram, Slack - communicate with customers on their preferred channel.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title">Analytics Dashboard</h3>
                <p class="feature-description">Track performance, monitor leads, and measure ROI from one central dashboard.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <h2 class="cta-title">Ready to Automate Your Business?</h2>
            <p class="cta-subtitle">Join hundreds of businesses using PropAI to save time and grow faster.</p>
            <div class="cta-buttons">
                <a href="/register" class="btn btn-white btn-lg">Start Free Trial</a>
                <a href="#demo" class="btn btn-outline-light btn-lg">Schedule Demo</a>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.bundle-card, .feature-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, { threshold: 0.1 });
    
    cards.forEach(card => observer.observe(card));
});
</script>
@endsection
