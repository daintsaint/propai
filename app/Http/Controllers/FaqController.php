<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display the FAQ page.
     */
    public function index()
    {
        $faqs = [
            [
                'question' => 'What is PropAI?',
                'answer' => 'PropAI is an intelligent platform designed to streamline property management and real estate operations using artificial intelligence.'
            ],
            [
                'question' => 'How do I get started?',
                'answer' => 'Simply sign up for an account and you can begin exploring our features. No credit card required for the basic plan.'
            ],
            [
                'question' => 'What features are available?',
                'answer' => 'PropAI offers property listing management, automated valuations, tenant screening, lease management, and analytics dashboards.'
            ],
            [
                'question' => 'Is my data secure?',
                'answer' => 'Yes, we use industry-standard encryption and security measures to protect your data. Your privacy is our top priority.'
            ],
            [
                'question' => 'Can I import existing property data?',
                'answer' => 'Absolutely! We support CSV imports and API integrations with major property management systems.'
            ]
        ];

        return view('faq', compact('faqs'));
    }
}
