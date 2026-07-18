<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class PageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. About Page Content
        PageContent::updateOrCreate(
            ['page_key' => 'about', 'section_key' => 'hero'],
            ['content_data' => [
                'title' => 'Built for Athletes,',
                'subtitle' => 'By Athletes',
                'description' => 'EAY Sports was founded with a single mission — give every team access to world-class custom sportswear without compromise on quality, price, or speed.',
            ]]
        );

        PageContent::updateOrCreate(
            ['page_key' => 'about', 'section_key' => 'stats'],
            ['content_data' => [
                ['value' => '50K+', 'label' => 'Jerseys Delivered'],
                ['value' => '1,200+', 'label' => 'Teams Served'],
                ['value' => '35+', 'label' => 'Countries Reached'],
                ['value' => '4.9★', 'label' => 'Average Rating'],
            ]]
        );

        PageContent::updateOrCreate(
            ['page_key' => 'about', 'section_key' => 'values'],
            ['content_data' => [
                ['icon' => 'Shield', 'title' => 'Premium Quality', 'desc' => 'Every jersey is crafted with top-grade fabric that lasts season after season.'],
                ['icon' => 'Zap', 'title' => 'Lightning Delivery', 'desc' => 'From design to doorstep in record time — we never keep your team waiting.'],
                ['icon' => 'Heart', 'title' => 'Made with Passion', 'desc' => 'Sports is in our DNA. We build gear for athletes who demand the best.'],
                ['icon' => 'Award', 'title' => 'Award Winning', 'desc' => 'Recognized for excellence in custom sportswear design and manufacturing.'],
            ]]
        );

        PageContent::updateOrCreate(
            ['page_key' => 'about', 'section_key' => 'team'],
            ['content_data' => [
                ['name' => 'Ahmad Raza', 'role' => 'CEO & Founder', 'initials' => 'AR', 'gradient' => 'from-indigo-600 to-indigo-500'],
                ['name' => 'Sara Khan', 'role' => 'Head of Design', 'initials' => 'SK', 'gradient' => 'from-[#7C3AED] to-[#DB2777]'],
                ['name' => 'Usman Ali', 'role' => 'Lead Engineer', 'initials' => 'UA', 'gradient' => 'from-[#0EA5E9] to-[#4F46E5]'],
                ['name' => 'Fatima Noor', 'role' => 'Operations Manager', 'initials' => 'FN', 'gradient' => 'from-[#DB2777] to-[#F59E0B]'],
            ]]
        );

        // 2. FAQ Page Content
        PageContent::updateOrCreate(
            ['page_key' => 'faq', 'section_key' => 'hero'],
            ['content_data' => [
                'title' => 'Frequently Asked Questions',
                'description' => "Need quick answers about sizing, customization, delivery, or custom bulk orders? We've got you covered.",
            ]]
        );

        PageContent::updateOrCreate(
            ['page_key' => 'faq', 'section_key' => 'faqs'],
            ['content_data' => [
                [
                    'category' => 'general',
                    'q' => 'What makes EAY Sports jerseys unique?',
                    'a' => 'Every EAY Sports jersey is constructed from specialized dry-fit polyester featuring moisture-wicking and antimicrobial characteristics. We utilize premium sublimation printing which ensures your customized team graphics never crack, fade, or peel.',
                ],
                [
                    'category' => 'general',
                    'q' => 'Can I design a custom design offline?',
                    'a' => 'Our online 3D customizer studio offers complete power. However, if you require specialized vectors or complex custom layout illustrations, our corporate design team is happy to assist. Please drop us a message via the Contact page.',
                ],
                [
                    'category' => 'orders',
                    'q' => 'Is there a minimum order quantity (MOQ) for wholesale partners?',
                    'a' => 'Wholesale partners can place orders with a highly flexible MOQ. Bulk order quantity starting discounts apply to orders of 15 units or more. You can customize different designs for different batch sizes seamlessly.',
                ],
                [
                    'category' => 'orders',
                    'q' => 'How long does production take?',
                    'a' => 'Once your 3D design is finalized and the order is approved, custom apparel manufacturing typically takes 7-10 business days. Large-scale bulk orders (100+ units) may require 12-15 days depending on season peak volume.',
                ],
                [
                    'category' => 'shipping',
                    'q' => 'Do you offer worldwide delivery?',
                    'a' => 'Yes, EAY Sports delivers custom sportswear worldwide. Standard carrier partners ensure delivery within 3-5 shipping days across North America, Europe, and Asia.',
                ],
                [
                    'category' => 'payment',
                    'q' => 'What payment terms are offered to wholesale dealers?',
                    'a' => 'We support safe credit cards, bank wire transfers, and localized payment channels. Direct B2B wholesale dealers can get net-term credit approvals upon verification by our financial relations team.',
                ],
                [
                    'category' => 'returns',
                    'q' => 'What is EAY Sports return policy for customized items?',
                    'a' => 'Because each jersey and customized apparel is manufactured bespoke for your team, custom orders are final. However, in the rare event of spelling errors, size mismatches, or fabric damage due to manufacturing, we will replace the item instantly free of charge.',
                ],
            ]]
        );

        // 3. Privacy Policy Content
        PageContent::updateOrCreate(
            ['page_key' => 'privacy', 'section_key' => 'hero'],
            ['content_data' => [
                'title' => 'PRIVACY POLICY',
                'description' => 'How we protect, manage, and safeguard your corporate files and business details.',
                'last_updated' => 'Last Updated: May 2026',
            ]]
        );

        PageContent::updateOrCreate(
            ['page_key' => 'privacy', 'section_key' => 'sections'],
            ['content_data' => [
                [
                    'id' => 'commitment',
                    'icon' => 'ShieldCheck',
                    'title' => 'Our Commitment To Your Privacy',
                    'content' => 'At EAY Sports, we value the trust you place in our custom sportswear solutions. This Privacy Policy details how we manage and safeguard your wholesale business account data, storefront actions, and private proprietary designs. We are committed to maintaining the confidentiality, integrity, and security of all personal and corporate information entrusted to us.',
                ],
                [
                    'id' => 'collect',
                    'icon' => 'Lock',
                    'title' => '1. Information We Collect',
                    'content' => 'We collect data necessary to manufacture custom products and secure partner storefront accounts. This includes your business name, representative credentials, corporate shipping addresses, contact telephone, tax identifiers, and vector or 3D customizer image files submitted during layout design.',
                ],
                [
                    'id' => 'use',
                    'icon' => 'Eye',
                    'title' => '2. How We Use Information',
                    'content' => 'Collected details are used directly to fulfill wholesale orders, customize team assets, route parcel shipments via international carriers, calculate tax rates, process invoice credits, authenticate secure dashboard actions, and send critical production step notifications.',
                ],
                [
                    'id' => 'security',
                    'icon' => 'ShieldAlert',
                    'title' => '3. Data Security Measures',
                    'content' => 'EAY Sports employs bank-grade database security systems. All active dealer passwords, customer credit card authorization payloads, and private 3D asset vector data are transmitted over high-grade secure layers (TLS/SSL) and stored with advanced encryption methods.',
                ],
                [
                    'id' => 'cookies',
                    'icon' => 'RefreshCw',
                    'title' => '4. Cookies & Trackers Policy',
                    'content' => 'We utilize localized browser tracking to persist your active customizer session and retain storefront shopping cart layouts. Localized indicators do not track external browsing history or compromise partner credentials.',
                ],
                [
                    'id' => 'contact',
                    'icon' => 'FileText',
                    'title' => '5. Legal Inquiries & Contact',
                    'content' => 'If you have concerns about corporate data handling, data deletion requests, or custom artwork licensing compliance, feel free to reach out to our legal department. We will address your concerns within 48 business hours.',
                ],
            ]]
        );

        // 4. Terms of Service Content
        PageContent::updateOrCreate(
            ['page_key' => 'terms', 'section_key' => 'hero'],
            ['content_data' => [
                'title' => 'TERMS OF SERVICE',
                'description' => 'Please read these terms carefully before customising apparel or initiating bulk order requests.',
                'effective_date' => 'Effective Date: May 2026',
            ]]
        );

        PageContent::updateOrCreate(
            ['page_key' => 'terms', 'section_key' => 'sections'],
            ['content_data' => [
                [
                    'id' => 'acceptance',
                    'icon' => 'Scale',
                    'title' => 'Acceptance of Terms',
                    'content' => 'By accessing the storefront, utilizing our interactive 3D apparel customizer, or registering as a B2B partner, you agree to comply with and be bound by the following Terms of Service. Please read them carefully. If you do not agree with any part of these terms, you must discontinue your use of our platform and customizer services immediately.',
                ],
                [
                    'id' => 'wholesale',
                    'icon' => 'BookOpen',
                    'title' => '1. Wholesale Business Account',
                    'content' => 'By registering as a wholesale dealer, you represent that you hold valid corporate tax credentials, business status, or localized team-sports uniform authorization status. You are solely responsible for keeping login credentials confidential and monitoring all activities under your wholesale login. EAY Sports reserves the right to suspend accounts with suspicious or unauthorized activities.',
                ],
                [
                    'id' => 'customizer',
                    'icon' => 'Sparkles',
                    'title' => '2. 3D Customizer Design Guidelines',
                    'content' => 'All custom apparel designs, custom logo vectors, and colors created or uploaded inside the EAY 3D customizer studio must not infringe on existing trademarks, copyrights, or official team logo rights. EAY Sports reserves the right to reject production on items featuring unauthorized intellectual property or offensive graphics.',
                ],
                [
                    'id' => 'production',
                    'icon' => 'Award',
                    'title' => '3. Customized Production & Fulfillments',
                    'content' => 'Because custom team jerseys, hoodies, and jackets are manufactured bespoke based on your dimensional orders, production commences immediately upon receipt of credit authorization. Orders cannot be cancelled, returned, or altered after production authorization has been granted.',
                ],
                [
                    'id' => 'quality',
                    'icon' => 'ShieldAlert',
                    'title' => '4. Fabric Specifications & Quality',
                    'content' => 'While we strive to match 3D customizer visual models as accurately as possible, slight color variations may occur between physical dry-fit sublimation fabrics and digital models. If products carry clear sewing defects, spelling errors, or fabric damage, replacements are processed instantly upon verification.',
                ],
                [
                    'id' => 'governance',
                    'icon' => 'ShieldCheck',
                    'title' => '5. Governance & Jurisdiction',
                    'content' => 'These terms are governed and construed in accordance with the laws of the State. EAY Sports reserves the right to modify these terms at any time. Partner updates are posted promptly. Continued use of the platform after updates constitutes acceptance of the new terms.',
                ],
            ]]
        );
    }
}
