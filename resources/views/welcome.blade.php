@extends('layouts.app')

@section('title', 'Welcome - PropAI')

@section('content')
<div class="welcome-page">
    <div style="text-align: center; padding: 4rem 0;">
        <h1 style="font-size: 3rem; font-weight: 800; color: #1f2937; margin-bottom: 1.5rem; line-height: 1.2;">
            Welcome to <span style="color: #2563eb;">PropAI</span>
        </h1>
        <p style="font-size: 1.25rem; color: #6b7280; max-width: 700px; margin: 0 auto 2rem; line-height: 1.8;">
            The intelligent platform for modern property management. Streamline your operations, 
            automate workflows, and make data-driven decisions with AI-powered insights.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="/faq" style="display: inline-block; background: #2563eb; color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 1.1rem; transition: background 0.3s;">
                Learn More
            </a>
            <a href="#" style="display: inline-block; background: white; color: #2563eb; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 1.1rem; border: 2px solid #2563eb; transition: all 0.3s;">
                Get Started
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 4rem; padding: 2rem 0;">
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
            <div style="width: 4rem; height: 4rem; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                🤖
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">
                AI-Powered
            </h3>
            <p style="color: #6b7280; line-height: 1.7;">
                Leverage artificial intelligence to automate property valuations, tenant screening, and market analysis.
            </p>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
            <div style="width: 4rem; height: 4rem; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                📊
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">
                Analytics
            </h3>
            <p style="color: #6b7280; line-height: 1.7;">
                Get real-time insights into your portfolio performance with comprehensive dashboards and reports.
            </p>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;">
            <div style="width: 4rem; height: 4rem; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                🔒
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">
                Secure
            </h3>
            <p style="color: #6b7280; line-height: 1.7;">
                Enterprise-grade security with encryption, access controls, and audit logs to protect your data.
            </p>
        </div>
    </div>
</div>

<style>
    .welcome-page {
        padding: 2rem 0;
    }

    @media (max-width: 768px) {
        .welcome-page h1 {
            font-size: 2rem !important;
        }
        
        .welcome-page p {
            font-size: 1rem !important;
        }
    }
</style>
@endsection
