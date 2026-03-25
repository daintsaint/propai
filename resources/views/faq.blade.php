@extends('layouts.app')

@section('title', 'FAQ - PropAI')

@section('content')
<div class="faq-page">
    <div style="text-align: center; margin-bottom: 3rem;">
        <h1 style="font-size: 2.5rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">
            Frequently Asked Questions
        </h1>
        <p style="font-size: 1.125rem; color: #6b7280; max-width: 600px; margin: 0 auto;">
            Find answers to common questions about PropAI and how to get the most out of our platform.
        </p>
    </div>

    <div style="max-width: 800px; margin: 0 auto;">
        @foreach($faqs as $index => $faq)
        <div style="background: white; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: #2563eb; margin-bottom: 0.75rem; display: flex; align-items: start;">
                <span style="background: #dbeafe; color: #2563eb; width: 2rem; height: 2rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; flex-shrink: 0; font-size: 0.875rem;">
                    {{ $index + 1 }}
                </span>
                {{ $faq['question'] }}
            </h2>
            <p style="color: #4b5563; line-height: 1.7; margin-left: 3rem;">
                {{ $faq['answer'] }}
            </p>
        </div>
        @endforeach
    </div>

    <div style="text-align: center; margin-top: 3rem; padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h3 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">
            Still have questions?
        </h3>
        <p style="color: #6b7280; margin-bottom: 1.5rem;">
            Our support team is here to help you with any questions you may have.
        </p>
        <a href="mailto:support@propai.com" style="display: inline-block; background: #2563eb; color: white; padding: 0.75rem 2rem; border-radius: 6px; text-decoration: none; font-weight: 600; transition: background 0.3s;">
            Contact Support
        </a>
    </div>
</div>

<style>
    .faq-page {
        padding: 2rem 0;
    }

    @media (max-width: 768px) {
        .faq-page h1 {
            font-size: 2rem !important;
        }
    }
</style>
@endsection
