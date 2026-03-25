@extends('layouts.app')

@section('title', 'Sign In - PropAI')

@section('content')
<section class="auth-section">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Sign in to your account to continue</p>
            </div>

            <form class="auth-form" id="loginForm" method="POST" action="/login">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="john@example.com" required autofocus>
                    <span class="error-message" id="emailError"></span>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Your password" required>
                    <span class="error-message" id="passwordError"></span>
                </div>

                <div class="form-group form-row">
                    <div class="form-checkbox">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember" class="checkbox-label">Remember me</label>
                    </div>
                    <a href="/forgot-password" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitBtn">
                    <span class="btn-text">Sign In</span>
                    <span class="btn-loading" style="display: none;">Signing in...</span>
                </button>

                <div class="auth-divider">
                    <span>or</span>
                </div>

                <button type="button" class="btn btn-outline btn-block">
                    <svg class="btn-icon" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Sign in with Google
                </button>

                @if ($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
                @endif

                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="/register">Sign up</a></p>
            </div>
        </div>

        <div class="auth-benefits">
            <h3>Quick Access</h3>
            <ul class="benefits-list">
                <li><span class="check">✓</span> View your AI agents</li>
                <li><span class="check">✓</span> Manage subscriptions</li>
                <li><span class="check">✓</span> Track leads & analytics</li>
                <li><span class="check">✓</span> Access support</li>
            </ul>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));
    
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline';
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (response.ok) {
            if (data.token) {
                localStorage.setItem('auth_token', data.token);
            }
            window.location.href = '/dashboard';
        } else {
            if (data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const errorEl = document.getElementById(key + 'Error');
                    const inputEl = document.getElementById(key);
                    if (errorEl) errorEl.textContent = data.errors[key][0];
                    if (inputEl) inputEl.classList.add('error');
                });
            } else if (data.message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-error';
                alertDiv.textContent = data.message;
                submitBtn.parentNode.insertBefore(alertDiv, submitBtn.nextSibling);
            }
            
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
        }
    } catch (error) {
        console.error('Login error:', error);
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-error';
        alertDiv.textContent = 'An error occurred. Please try again.';
        submitBtn.parentNode.insertBefore(alertDiv, submitBtn.nextSibling);
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
    }
});

const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('error')) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-error';
    alertDiv.textContent = 'Invalid credentials. Please try again.';
    const form = document.getElementById('loginForm');
    form.insertBefore(alertDiv, form.firstChild);
}
</script>
@endsection
