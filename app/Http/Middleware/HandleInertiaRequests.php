<?php

namespace App\Http\Middleware;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $settingsPath = storage_path('app/settings.json');
        $settings = [];
        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
        }

        $showReviews = $settings['show_reviews'] ?? true;
        $showPricesGlobal = $settings['show_prices_global'] ?? true;

        $footerEmail = $settings['footer_email'] ?? 'info@eaysports.com';
        $footerPhone = $settings['footer_phone'] ?? '+1 (555) 123-4567';
        $footerAddress = $settings['footer_address'] ?? '123 Sports Avenue, NY 10001';
        $footerDescription = $settings['footer_description'] ?? 'Your premier destination for custom sportswear. We deliver quality, performance, and style to athletes and teams worldwide.';

        $socialFacebook = $settings['social_facebook'] ?? 'https://facebook.com';
        $socialTwitter = $settings['social_twitter'] ?? 'https://twitter.com';
        $socialInstagram = $settings['social_instagram'] ?? 'https://instagram.com';
        $socialLinkedin = $settings['social_linkedin'] ?? 'https://linkedin.com';

        $companyLinks = $settings['company_links'] ?? [
            ['label' => 'About Us', 'href' => '/about'],
            ['label' => 'Contact', 'href' => '/contact'],
            ['label' => 'Find Dealer', 'href' => '/dealer-locator'],
            ['label' => 'FAQ', 'href' => '/faq'],
        ];

        $productsLinks = $settings['products_links'] ?? [
            ['label' => 'Custom Sportswear', 'href' => '/products'],
            ['label' => 'Custom Builder', 'href' => '/builder'],
            ['label' => 'Bulk Orders', 'href' => '/products'],
            ['label' => 'Privacy Policy', 'href' => '/privacy-policy'],
        ];

        $supportLinks = $settings['support_links'] ?? [
            ['label' => 'Shipping Info', 'href' => '/faq'],
            ['label' => 'Returns', 'href' => '/faq'],
            ['label' => 'Size Guide', 'href' => '/faq'],
            ['label' => 'Terms of Service', 'href' => '/terms-of-service'],
        ];

        return [
            ...parent::share($request),
            'settings' => [
                'show_reviews' => (bool) $showReviews,
                'show_prices_global' => (bool) $showPricesGlobal,
                'footer_email' => $footerEmail,
                'footer_phone' => $footerPhone,
                'footer_address' => $footerAddress,
                'footer_description' => $footerDescription,
                'social_facebook' => $socialFacebook,
                'social_twitter' => $socialTwitter,
                'social_instagram' => $socialInstagram,
                'social_linkedin' => $socialLinkedin,
                'company_links' => $companyLinks,
                'products_links' => $productsLinks,
                'support_links' => $supportLinks,
            ],
            'categories' => Category::whereNull('parent_id')
                ->where('status', true)
                ->with(['subcategories' => function ($q) {
                    $q->where('status', true);
                }])
                ->get(),
            'headerCategories' => Category::whereNull('parent_id')
                ->where('status', true)
                ->with(['subcategories' => function ($q) {
                    $q->where('status', true);
                }])
                ->get(),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'last_name' => $request->user()->last_name,
                    'email' => $request->user()->email,
                    'phone' => $request->user()->phone,
                    'profile_image' => $request->user()->profile_image,
                    'email_notifications' => (bool) $request->user()->email_notifications,
                    'newsletter' => (bool) $request->user()->newsletter,
                    'two_factor_auth' => (bool) $request->user()->two_factor_auth,
                    'role' => $request->user()->role,
                ] : null,
                'dealer' => ($request->user() && $request->user()->role === 'dealer') ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                ] : null,
            ],
        ];
    }
}
