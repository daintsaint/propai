/**
 * PropAI - Main Application JavaScript
 * Handles API interactions, form submissions, and dynamic content
 */

const API_BASE = '/api';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

function setCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (token) {
        return {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        };
    }
    return {};
}

async function apiRequest(endpoint, options = {}) {
    const defaultOptions = {
        headers: {
            ...setCSRFToken(),
            'Content-Type': 'application/json',
            ...options.headers,
        },
    };

    const config = { ...defaultOptions, ...options };
    if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
        config.body = JSON.stringify(config.body);
    }

    try {
        const response = await fetch(`${API_BASE}${endpoint}`, config);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'An error occurred');
        }

        return { success: true, data };
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, error: error.message };
    }
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed; top: 20px; right: 20px; padding: 1rem 1.5rem;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white; border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });

        document.addEventListener('click', function(e) {
            if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
            }
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    loadBundles();
    initializeForms();
});

async function loadBundles() {
    const bundlesGrid = document.getElementById('bundlesGrid');
    if (!bundlesGrid) return;

    const result = await apiRequest('/verticals');
    if (result.success && result.data.data) {
        renderBundles(result.data.data, bundlesGrid);
    }
}

function renderBundles(bundles, container) {
    if (!Array.isArray(bundles) || bundles.length === 0) return;

    container.innerHTML = bundles.map(bundle => {
        const isFeatured = bundle.slug === 'local-business';
        const badge = isFeatured ? '<span class="bundle-badge featured-badge">Best Value</span>' : '';
        const featuredClass = isFeatured ? 'featured' : '';

        return `
            <div class="bundle-card ${featuredClass}" data-bundle="${bundle.slug}">
                <div class="bundle-header">
                    <span class="bundle-icon">${getBundleIcon(bundle.slug)}</span>
                    ${badge}
                </div>
                <h3 class="bundle-title">${bundle.name}</h3>
                <p class="bundle-description">${bundle.description}</p>
                <div class="bundle-price">
                    <span class="price-currency">$</span>
                    <span class="price-amount">${bundle.monthly_price}</span>
                    <span class="price-period">/month</span>
                </div>
                <ul class="bundle-features">
                    ${bundle.features.map(feature => `
                        <li><span class="check">✓</span> ${feature}</li>
                    `).join('')}
                </ul>
                <a href="/bundles/${bundle.slug}" class="btn btn-primary btn-block">View Details</a>
            </div>
        `;
    }).join('');
}

function getBundleIcon(slug) {
    const icons = {
        'real-estate': '🏠',
        'ecommerce': '🛒',
        'local-business': '🏪',
        'professional': '💼',
        'saas': '🚀'
    };
    return icons[slug] || '⭐';
}

function initializeForms() {
    initializeRegisterForm();
    initializeLoginForm();
    initializeSubscribeForm();
}

function initializeRegisterForm() {
    const form = document.getElementById('registerForm');
    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const btnText = submitBtn?.querySelector('.btn-text');
        const btnLoading = submitBtn?.querySelector('.btn-loading');

        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));

        if (submitBtn) {
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
        }

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        const result = await apiRequest('/register', { method: 'POST', body: data });

        if (result.success) {
            showToast('Account created successfully! Redirecting...', 'success');
            setTimeout(() => { window.location.href = '/dashboard'; }, 1000);
        } else {
            handleFormErrors(result.error, data);
            if (submitBtn) {
                submitBtn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            }
        }
    });
}

function initializeLoginForm() {
    const form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const btnText = submitBtn?.querySelector('.btn-text');
        const btnLoading = submitBtn?.querySelector('.btn-loading');

        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));

        if (submitBtn) {
            submitBtn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
        }

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        const result = await apiRequest('/login', { method: 'POST', body: data });

        if (result.success) {
            if (result.data.token) {
                localStorage.setItem('auth_token', result.data.token);
            }
            showToast('Login successful! Redirecting...', 'success');
            setTimeout(() => { window.location.href = '/dashboard'; }, 1000);
        } else {
            handleFormErrors(result.error, data);
            if (submitBtn) {
                submitBtn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            }
        }
    });
}

function initializeSubscribeForm() {
    const form = document.getElementById('subscribeForm');
    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
        }

        const action = form.action;
        const formData = new FormData(this);

        try {
            const response = await fetch(action, {
                method: 'POST',
                headers: setCSRFToken(),
                body: formData,
            });

            const data = await response.json();

            if (response.ok) {
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    showToast('Subscription initiated! Check your email.', 'success');
                }
            } else {
                showToast(data.message || 'Subscription failed. Please try again.', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            }
        } catch (error) {
            console.error('Subscribe error:', error);
            showToast('An error occurred. Please try again.', 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        }
    });
}

function handleFormErrors(error, data) {
    if (typeof error === 'object' && error !== null) {
        Object.keys(error).forEach(key => {
            const errorEl = document.getElementById(key + 'Error');
            const inputEl = document.getElementById(key);
            if (errorEl) errorEl.textContent = error[key][0];
            if (inputEl) inputEl.classList.add('error');
        });
    } else if (typeof error === 'string') {
        showToast(error, 'error');
    }
}

const passwordInput = document.getElementById('password');
if (passwordInput) {
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

        if (password.length > 0 && passwordStrength) {
            passwordStrength.textContent = strengthLabels[strength - 1] || 'Very Weak';
            passwordStrength.style.color = strengthColors[strength - 1] || '#ff4757';
        } else if (passwordStrength) {
            passwordStrength.textContent = '';
            passwordStrength.style.color = '';
        }
    });
}

async function loadDashboardData() {
    const statsContainer = document.getElementById('dashboardStats');
    if (!statsContainer) return;

    const result = await apiRequest('/dashboard');
    if (result.success && result.data) {
        renderDashboardStats(result.data, statsContainer);
    }
}

function renderDashboardStats(data, container) {
    const stats = [
        { label: 'Active Agents', value: data.active_agents || 0, icon: '🤖' },
        { label: 'Total Leads', value: data.total_leads || 0, icon: '📊' },
        { label: 'Subscription', value: data.subscription_status || 'Inactive', icon: '💳' },
        { label: 'Actions This Month', value: data.actions_count || 0, icon: '⚡' },
    ];

    container.innerHTML = stats.map(stat => `
        <div class="dashboard-stat">
            <span class="stat-icon">${stat.icon}</span>
            <div class="stat-content">
                <span class="stat-value">${stat.value}</span>
                <span class="stat-label">${stat.label}</span>
            </div>
        </div>
    `).join('');
}

async function loadAgents() {
    const agentsContainer = document.getElementById('agentsList');
    if (!agentsContainer) return;

    const result = await apiRequest('/dashboard/agents');
    if (result.success && result.data) {
        renderAgents(result.data, agentsContainer);
    }
}

function renderAgents(agents, container) {
    if (!agents || agents.length === 0) {
        container.innerHTML = '<p class="empty-state">No active agents. Subscribe to a bundle to get started!</p>';
        return;
    }

    container.innerHTML = agents.map(agent => `
        <div class="agent-card">
            <div class="agent-header">
                <span class="agent-icon">${agent.agent_type === 'email' ? '📧' : '💬'}</span>
                <h3 class="agent-name">${agent.name}</h3>
                <span class="agent-status ${agent.active ? 'active' : 'inactive'}">
                    ${agent.active ? 'Active' : 'Inactive'}
                </span>
            </div>
            <p class="agent-description">${agent.description || 'AI Agent'}</p>
            <div class="agent-stats">
                <span>Actions: ${agent.actions_count || 0}</span>
                <span>Last Active: ${agent.last_active_at ? new Date(agent.last_active_at).toLocaleDateString() : 'Never'}</span>
            </div>
        </div>
    `).join('');
}

async function loadLeads() {
    const leadsContainer = document.getElementById('leadsList');
    if (!leadsContainer) return;

    const result = await apiRequest('/dashboard/leads');
    if (result.success && result.data) {
        renderLeads(result.data, leadsContainer);
    }
}

function renderLeads(leads, container) {
    if (!leads || leads.length === 0) {
        container.innerHTML = '<p class="empty-state">No leads yet.</p>';
        return;
    }

    container.innerHTML = `
        <table class="leads-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Source</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                ${leads.map(lead => `
                    <tr>
                        <td>${lead.name}</td>
                        <td>${lead.email}</td>
                        <td><span class="status-badge status-${lead.status}">${lead.status}</span></td>
                        <td>${lead.source || 'Unknown'}</td>
                        <td>${new Date(lead.created_at).toLocaleDateString()}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

if (document.getElementById('dashboardStats')) {
    loadDashboardData();
    loadAgents();
    loadLeads();
}

window.PropAI = {
    apiRequest,
    showToast,
    loadBundles,
    loadDashboardData,
    loadAgents,
    loadLeads,
};
