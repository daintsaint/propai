<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $bundle->name }} | PropAI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .checkout-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }
        
        .checkout-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .checkout-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .checkout-header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .checkout-body {
            padding: 40px;
        }
        
        .bundle-summary {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .bundle-name {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        
        .bundle-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .pricing-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e1e5eb;
        }
        
        .pricing-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .pricing-value {
            font-size: 20px;
            font-weight: 700;
            color: #667eea;
        }
        
        .features-list {
            margin: 30px 0;
        }
        
        .features-list h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }
        
        .features-list ul {
            list-style: none;
        }
        
        .features-list li {
            padding: 10px 0;
            color: #555;
            display: flex;
            align-items: center;
        }
        
        .features-list li:before {
            content: "✓";
            color: #10b981;
            font-weight: bold;
            margin-right: 10px;
            font-size: 18px;
        }
        
        .checkout-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            flex: 1;
            padding: 16px 32px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
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
        
        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            color: #10b981;
            font-size: 14px;
        }
        
        .secure-badge svg {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>🚀 Complete Your Subscription</h1>
            <p>Secure checkout powered by Stripe</p>
        </div>
        
        <div class="checkout-body">
            <div class="bundle-summary">
                <div class="bundle-name">{{ $bundle->name }} Bundle</div>
                <div class="bundle-description">{{ $bundle->description }}</div>
                
                <div class="pricing-grid">
                    <div class="pricing-item">
                        <div class="pricing-label">Monthly Price</div>
                        <div class="pricing-value">${{ $bundle->monthly_price }}</div>
                    </div>
                    <div class="pricing-item">
                        <div class="pricing-label">Billing Cycle</div>
                        <div class="pricing-value">Monthly</div>
                    </div>
                    <div class="pricing-item">
                        <div class="pricing-label">Setup Fee</div>
                        <div class="pricing-value">$0</div>
                    </div>
                </div>
            </div>
            
            <div class="features-list">
                <h3>What's Included:</h3>
                <ul>
                    @foreach(explode("\n", $bundle->features) as $feature)
                        @if(trim($feature))
                            <li>{{ trim($feature) }}</li>
                        @endif
                    @endforeach
                    <li>24/7 Email Support</li>
                    <li>Cancel Anytime</li>
                </ul>
            </div>
            
            <form action="{{ route('payment.checkout.process', $bundle->slug) }}" method="POST">
                @csrf
                <div class="checkout-actions">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                    <button type="submit" class="btn btn-primary">
                        Proceed to Payment →
                    </button>
                </div>
            </form>
            
            <div class="secure-badge">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <span>Secure SSL encryption powered by Stripe</span>
            </div>
        </div>
    </div>
</body>
</html>
