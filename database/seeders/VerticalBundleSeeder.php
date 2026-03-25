<?php

namespace Database\Seeders;

use App\Models\VerticalBundle;
use Illuminate\Database\Seeder;

class VerticalBundleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bundles = [
            [
                'name' => 'Real Estate Agent',
                'slug' => 'real-estate',
                'description' => 'AI-powered automation for real estate agents. Automate lead follow-ups, property inquiries, and client communication across email and Telegram.',
                'features' => [
                    'Automated lead follow-up emails',
                    'Telegram bot for instant property inquiries',
                    'CRM synchronization',
                    'Appointment scheduling automation',
                    'Property listing updates',
                ],
                'monthly_price' => 49.00,
                'setup_instructions' => 'Connect your email account, configure Telegram bot token, and import your CRM contacts to get started.',
                'is_active' => true,
            ],
            [
                'name' => 'E-commerce Store',
                'slug' => 'ecommerce',
                'description' => 'Complete automation for online stores. Handle customer support, order updates, and abandoned cart recovery automatically.',
                'features' => [
                    'Abandoned cart recovery emails',
                    'Order status notifications via Telegram',
                    'Customer support auto-responses',
                    'Product recommendation engine',
                    'Review collection automation',
                ],
                'monthly_price' => 79.00,
                'setup_instructions' => 'Connect your Shopify/WooCommerce store, configure email templates, and set up Telegram notifications.',
                'is_active' => true,
            ],
            [
                'name' => 'Local Business',
                'slug' => 'local-business',
                'description' => 'Perfect for restaurants, salons, and service businesses. Manage bookings, reviews, and customer communication effortlessly.',
                'features' => [
                    'Appointment booking automation',
                    'Review request campaigns',
                    'SMS and email notifications',
                    'Customer feedback collection',
                    'Promotional campaign automation',
                ],
                'monthly_price' => 39.00,
                'setup_instructions' => 'Set up your business hours, configure booking preferences, and connect your communication channels.',
                'is_active' => true,
            ],
            [
                'name' => 'Professional Services',
                'slug' => 'professional-services',
                'description' => 'For consultants, lawyers, accountants, and coaches. Streamline client onboarding and communication.',
                'features' => [
                    'Client onboarding automation',
                    'Document collection workflows',
                    'Meeting scheduling and reminders',
                    'Invoice and payment reminders',
                    'Client portal integration',
                ],
                'monthly_price' => 99.00,
                'setup_instructions' => 'Configure your service offerings, set up document templates, and connect your payment processor.',
                'is_active' => true,
            ],
            [
                'name' => 'SaaS Startup',
                'slug' => 'saas-startup',
                'description' => 'Growth automation for SaaS companies. Handle user onboarding, engagement, and retention at scale.',
                'features' => [
                    'User onboarding email sequences',
                    'In-app message automation',
                    'Churn prevention workflows',
                    'Feature adoption campaigns',
                    'Customer health scoring',
                ],
                'monthly_price' => 149.00,
                'setup_instructions' => 'Integrate with your product analytics, configure user segments, and set up engagement triggers.',
                'is_active' => true,
            ],
        ];

        foreach ($bundles as $bundle) {
            VerticalBundle::firstOrCreate(
                ['slug' => $bundle['slug']],
                $bundle
            );
        }
    }
}
