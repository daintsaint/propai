@extends('layouts.app')

@section('title', 'Create Account - PropAI')

@section('content')
<section class="auth-section">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Start your 14-day free trial today</p>
            </div>

            <form class="auth-form" id="registerForm" method="POST" action="/register">
                @csrf
                
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="John Doe" required autofocus>
                    <span class="error-message" id="nameError"></span>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="john@example.com" required>
                    <span class="error-message" id="emailError"></span>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Min. 8 characters" minlength="8" required>
                    <span class="error-message" id="passwordError"></span>
                    <div class="password-strength" id="passwordStrength"></div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Confirm your password" required>
                </div>

                <div class="form-group">
                    <label for="company" class="form-label">Company (Optional)</label>
                    <input type="text" id="company" name="company" class="form-input" placeholder="Your company name">
                </div>

                <div class="form-checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms" class="checkbox-label">
                        I agree to the <a href="/terms" target="_blank">Terms of Service</a> and 
                        <a href="/privacy" target="_blank">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitBtn">
                    <span class="btn-text">Create Account</span>
                    <span class="btn-loading" style="display: none;">Creating Account...</span>
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
                    Sign up with Google
                </button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="/login">Sign in</a></p>
            </div>
        </div>

        <div class="auth-benefits">
            <h3>What you get:</h3>
            <ul class="benefits-list">
                <li><span class="check">✓</span> 14-day free trial</li>
                <li><span class="check">✓</span> No credit card required</li>
                <li><span class="check">✓</span> Full access to all features</li>
                <li><span class="check">✓</span> Cancel anytime</li>
                <li><span class="check">✓</span> 24/7 support</li>
            </ul>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
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
        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (response.ok) {
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
                alert(data.message);
            }
            
            submitBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
        }
    } catch (error) {
        console.error('Registration error:', error);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
    }
});

const passwordInput = document.getElementById('password');
const passwordStrength = document.getElementById('passwordStrength');

passwordInput.addEventListener('input', function() {
    const password = this.value;
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    const strengthLabels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const strengthColors = ['#ff4757', '#ffa502', '#ffdd59', '#7bed9f', '#2ed573'];
    
    if (password.length > 0) {
        passwordStrength.textContent = strengthLabels[strength - 1] || 'Very Weak';
        passwordStrength.style.color = strengthColors[strength - 1] || '#ff4757';
    } else {
        passwordStrength.textContent = '';
        passwordStrength.style.color = '';
    }
});
</script>
@endsection
