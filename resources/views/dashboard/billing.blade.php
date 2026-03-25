@extends('layouts.app')

@section('title', 'Billing & Subscription')

@section('content')
<div class="billing-container">
    <div class="billing-header">
        <h1>💳 Billing & Subscription</h1>
        <p>Manage your subscription and payment details</p>
    </div>

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($subscription && $subscription->status === 'active')
        <!-- Active Subscription -->
        <div class="subscription-card active">
            <div class="subscription-header">
                <div class="status-badge active">Active</div>
                <h2>{{ $subscription->verticalBundle->name }} Bundle</h2>
            </div>
            
            <div class="subscription-details">
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Monthly Price</label>
                        <div class="value">${{ $subscription->monthly_price }}/month</div>
                    </div>
                    <div class="detail-item">
                        <label>Billing Cycle</label>
                        <div class="value">Monthly</div>
                    </div>
                    <div class="detail-item">
                        <label>Started</label>
                        <div class="value">{{ $subscription->starts_at->format('M d, Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <label>Next Billing</label>
                        <div class="value">{{ $subscription->starts_at->addMonth()->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>

            <div class="subscription-features">
                <h3>Included Features:</h3>
                <ul>
                    @foreach(explode("\n", $subscription->verticalBundle->features) as $feature)
                        @if(trim($feature))
                            <li>✓ {{ trim($feature) }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <div class="subscription-actions">
                <a href="{{ route('payment.portal') }}" class="btn btn-primary" target="_blank">
                    Manage Billing
                </a>
                <form action="{{ route('subscriptions.cancel') }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel? You will lose access at the end of your billing period.')">
                        Cancel Subscription
                    </button>
                </form>
            </div>

            <div class="subscription-note">
                <p>💡 Need to update payment method or download invoices? Click "Manage Billing" to access your Stripe customer portal.</p>
            </div>
        </div>

    @else
        <!-- No Active Subscription -->
        <div class="no-subscription">
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h2>No Active Subscription</h2>
                <p>Choose a vertical bundle to get started with AI-powered automation</p>
            </div>

            <div class="bundles-grid">
                @foreach($bundles as $bundle)
                    <div class="bundle-card">
                        <div class="bundle-header">
                            <h3>{{ $bundle->name }}</h3>
                            <div class="price">${{ $bundle->monthly_price }}<span>/month</span></div>
                        </div>
                        
                        <p class="bundle-description">{{ $bundle->description }}</p>
                        
                        <ul class="bundle-features">
                            @foreach(array_slice(explode("\n", $bundle->features), 0, 4) as $feature)
                                @if(trim($feature))
                                    <li>✓ {{ trim($feature) }}</li>
                                @endif
                            @endforeach
                        </ul>
                        
                        <a href="{{ route('payment.checkout', $bundle->slug) }}" class="btn btn-primary btn-block">
                            Subscribe Now
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Billing History -->
    @if($subscription && $subscription->status === 'active')
        <div class="billing-history">
            <h3>📄 Billing History</h3>
            <p style="color: #666; margin-bottom: 20px;">
                Access your invoices and payment history through the Stripe customer portal.
            </p>
            <a href="{{ route('payment.portal') }}" class="btn btn-secondary" target="_blank">
                View Billing Portal
            </a>
        </div>
    @endif
</div>

<style>
    .billing-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .billing-header {
        margin-bottom: 40px;
    }

    .billing-header h1 {
        font-size: 32px;
        color: #333;
        margin-bottom: 10px;
    }

    .billing-header p {
        color: #666;
        font-size: 16px;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .alert-error {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .alert-success {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }

    .subscription-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 40px;
        margin-bottom: 30px;
        border-left: 6px solid #10b981;
    }

    .subscription-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
    }

    .status-badge.active {
        background: #d1fae5;
        color: #059669;
    }

    .subscription-header h2 {
        font-size: 28px;
        color: #333;
    }

    .subscription-details {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .detail-item label {
        display: block;
        font-size: 14px;
        color: #666;
        margin-bottom: 5px;
    }

    .detail-item .value {
        font-size: 20px;
        font-weight: 700;
        color: #333;
    }

    .subscription-features {
        margin-bottom: 30px;
        padding: 25px;
        background: #f0fdf4;
        border-radius: 12px;
        border: 2px solid #10b981;
    }

    .subscription-features h3 {
        color: #059669;
        margin-bottom: 15px;
    }

    .subscription-features ul {
        list-style: none;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 10px;
    }

    .subscription-features li {
        color: #047857;
        font-size: 15px;
    }

    .subscription-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .btn {
        padding: 14px 28px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    .btn-danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-danger:hover {
        background: #fecaca;
    }

    .btn-block {
        width: 100%;
        text-align: center;
    }

    .subscription-note {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 15px 20px;
    }

    .subscription-note p {
        color: #1e40af;
        font-size: 14px;
        margin: 0;
    }

    .no-subscription {
        text-align: center;
    }

    .empty-state {
        background: white;
        border-radius: 16px;
        padding: 60px 40px;
        margin-bottom: 40px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .empty-icon {
        font-size: 64px;
        margin-bottom: 20px;
    }

    .empty-state h2 {
        font-size: 28px;
        color: #333;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #666;
        font-size: 16px;
    }

    .bundles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    .bundle-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        text-align: left;
        transition: transform 0.3s ease;
    }

    .bundle-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }

    .bundle-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f1f5f9;
    }

    .bundle-header h3 {
        font-size: 22px;
        color: #333;
    }

    .price {
        font-size: 32px;
        font-weight: 700;
        color: #667eea;
    }

    .price span {
        font-size: 16px;
        color: #666;
        font-weight: 400;
    }

    .bundle-description {
        color: #666;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .bundle-features {
        list-style: none;
        margin-bottom: 25px;
    }

    .bundle-features li {
        padding: 8px 0;
        color: #555;
        font-size: 15px;
    }

    .billing-history {
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .billing-history h3 {
        font-size: 22px;
        color: #333;
        margin-bottom: 10px;
    }
</style>
@endsection
