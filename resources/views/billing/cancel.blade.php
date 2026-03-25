<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Cancelled | PropAI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .cancel-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            text-align: center;
            padding: 60px 40px;
        }
        
        .cancel-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        
        .cancel-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        
        .cancel-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        
        .cancel-message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .benefits {
            text-align: left;
            background: #fffbeb;
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
        }
        
        .benefits h3 {
            font-size: 18px;
            color: #92400e;
            margin-bottom: 15px;
        }
        
        .benefits ul {
            list-style: none;
            color: #78350f;
            line-height: 2;
        }
        
        .benefits li:before {
            content: "→ ";
            font-weight: bold;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
        
        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.4);
        }
        
        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }
        
        .btn-secondary:hover {
            background: #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="cancel-container">
        <div class="cancel-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        
        <h1 class="cancel-title">Checkout Cancelled</h1>
        <p class="cancel-message">
            No worries! Your checkout was cancelled. You can complete your subscription anytime.
        </p>
        
        <div class="benefits">
            <h3>🎯 What You're Missing:</h3>
            <ul>
                <li>Pre-built AI agents for your industry</li>
                <li>Automated lead management</li>
                <li>Telegram & Gmail integrations</li>
                <li>24/7 customer support</li>
                <li>Cancel anytime, no commitments</li>
            </ul>
        </div>
        
        <div class="btn-group">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
            <a href="{{ route('verticals.index') }}" class="btn btn-primary">View Bundles</a>
        </div>
    </div>
</body>
</html>
