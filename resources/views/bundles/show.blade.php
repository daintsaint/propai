@extends('layouts.app')

@section('title', $bundle['name'] . ' - PropAI')

@section('content')
<section class="bundle-detail-hero">
    <div class="container">
        <div class="breadcrumb">
            <a href="/">Home</a>
            <span>/</span>
            <a href="/#bundles">Bundles</a>
            <span>/</span>
            <span class="current">{{ $bundle['name'] }}</span>
        </div>
        <div class="bundle-detail-content">
            <div class="bundle-detail-info">
                <span class="bundle-detail-icon">{{ $bundle['icon'] }}</span>
                <h1 class="bundle-detail-title">{{ $bundle['name'] }}</h1>
                <p class="bundle-detail-description">{{ $bundle['description'] }}</p>
                <div class="bundle-detail-price">
                    <span class="price-currency">$</span>
                    <span class="price-amount">{{ $bundle['price'] }}</span>
                    <span class="price-period">/month</span>
                </div>
                <div class="bundle-detail-cta">
                    @auth
                        <form action="/bundles/{{ $bundle['slug'] }}/subscribe" method="POST" id="subscribeForm">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">Subscribe Now</button>
                        </form>
                    @else
                        <a href="/register" class="btn btn-primary btn-lg">Subscribe Now</a>
                    @endauth
                    <a href="#demo" class="btn btn-outline btn-lg">Watch Demo</a>
                </div>
                <div class="bundle-detail-meta">
                    <span class="meta-item">✓ No credit card required for trial</span>
                    <span class="meta-item">✓ 14-day free trial</span>
                    <span class="meta-item">✓ Cancel anytime</span>
                </div>
            </div>
            <div class="bundle-detail-visual">
                <div class="preview-card">
                    <div class="preview-header">
                        <span class="preview-dot"></span>
                        <span class="preview-dot"></span>
                        <span class="preview-dot"></span>
                    </div>
                    <div class="preview-body">
                        <div class="preview-agent">
                            <span class="agent-avatar">🤖</span>
                            <div class="agent-info">
                                <span class="agent-name">AI Agent</span>
                                <span class="agent-status">Active</span>
                            </div>
                        </div>
                        <div class="preview-stats">
                            <div class="preview-stat">
                                <span class="stat-value">24/7</span>
                                <span class="stat-label">Availability</span>
                            </div>
                            <div class="preview-stat">
                                <span class="stat-value">&lt;1s</span>
                                <span class="stat-label">Response Time</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bundle-features-section">
    <div class="container">
        <h2 class="section-title">What's Included</h2>
        <div class="features-list">
            @foreach($bundle['features'] as $feature)
            <div class="feature-item">
                <span class="feature-check">✓</span>
                <span class="feature-text">{{ $feature }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bundle-how-it-works">
    <div class="container">
        <h2 class="section-title">How It Works</h2>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3 class="step-title">Subscribe</h3>
                <p class="step-description">Choose your bundle and start your 14-day free trial. No credit card required.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3 class="step-title">Connect</h3>
                <p class="step-description">Link your email, calendar, and other tools. Takes less than 5 minutes.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3 class="step-title">Automate</h3>
                <p class="step-description">Your AI agent starts working immediately, handling tasks around the clock.</p>
            </div>
        </div>
    </div>
</section>

<section class="bundle-faq">
    <div class="container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <div class="faq-list">
            <div class="faq-item">
                <h3 class="faq-question">Can I cancel anytime?</h3>
                <p class="faq-answer">Yes, you can cancel your subscription at any time with no questions asked. Your service will continue until the end of your billing period.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">Is there a free trial?</h3>
                <p class="faq-answer">Yes! All bundles come with a 14-day free trial. No credit card required to start.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">Can I upgrade or downgrade later?</h3>
                <p class="faq-answer">Absolutely. You can upgrade or downgrade your bundle at any time from your dashboard.</p>
            </div>
            <div class="faq-item">
                <h3 class="faq-question">What integrations are supported?</h3>
                <p class="faq-answer">We support Gmail, Outlook, Slack, Telegram, Calendly, and many more. Check the setup guide for your specific bundle.</p>
            </div>
        </div>
    </div>
</section>
@endsection

<script>
const bundleData = @json($bundle);
</script>
