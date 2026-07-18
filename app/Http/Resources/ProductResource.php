<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ProductResource
 *
 * Centralizes the product data transformation in ONE place.
 * All controllers use this resource, so any future changes
 * (e.g., adding real ratings from a reviews table) only
 * need to be made here.
 */
class ProductResource extends JsonResource
{
    /**
     * Whether to include full details (description, features, images gallery).
     * Set to true when rendering the single product detail page.
     */
    public bool $detailed = false;

    public function withDetails(): static
    {
        $this->detailed = true;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $cat = $this->categories->first();
        $parentCatName = 'Accessories';
        $subCategorySlug = 'accessories';

        if ($cat) {
            $subCategorySlug = $cat->slug;
            $parentCatName = $cat->parent ? $cat->parent->name : $cat->name;
        }

        $featuredImage = $this->featured_image
            ? '/storage/'.$this->featured_image
            : 'https://images.unsplash.com/photo-1551280857-2b9bbe52acf4?w=400&h=400&fit=crop&q=80';

        $avgRating = isset($this->reviews_avg_rating)
            ? round($this->reviews_avg_rating, 1)
            : (round($this->reviews()->avg('rating') ?? 0, 1));

        $reviewsCount = isset($this->reviews_count)
            ? $this->reviews_count
            : $this->reviews()->count();

        $defaultColors = ['#1D4ED8', '#DC2626', '#16A34A', '#FFFFFF', '#1e293b', '#374151', '#4F46E5'];
        $defaultSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size'];

        $productColors = is_array($this->colors) && count($this->colors) > 0 ? $this->colors : $defaultColors;
        $productSizes = is_array($this->sizes) && count($this->sizes) > 0 ? $this->sizes : $defaultSizes;

        $colorsList = [
            '#1D4ED8' => 'Blue',
            '#DC2626' => 'Red',
            '#16A34A' => 'Green',
            '#FFFFFF' => 'White',
            '#1e293b' => 'Black',
            '#374151' => 'Gray',
            '#4F46E5' => 'Indigo',
        ];

        $base = [
            'id' => $this->id,
            'name' => $this->title,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'short_description' => $this->short_description,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'badge' => $this->is_featured ? 'Featured' : null,
            'customizable' => true,
            'rating' => $avgRating,
            'reviews' => $reviewsCount,
            'colors' => $productColors,
            'sizes' => $productSizes,
            'image' => $featuredImage,
            'category' => $parentCatName,
            'subcategory' => $subCategorySlug,
            'show_price' => (bool) $this->show_price,
        ];

        if ($this->detailed) {
            // Build full image gallery for product detail page
            $images = collect();
            $featuredPath = $this->featured_image ? '/storage/'.$this->featured_image : null;
            if ($featuredPath) {
                $images->push($featuredPath);
            }
            foreach ($this->images ?? [] as $img) {
                $galleryPath = '/storage/'.$img->image_path;
                if ($galleryPath !== $featuredPath) {
                    $images->push($galleryPath);
                }
            }
            if ($images->isEmpty()) {
                $images->push('https://images.unsplash.com/photo-1551280857-2b9bbe52acf4?w=800&h=1000&fit=crop&q=80');
            }

            $mappedColorsDetail = [];
            foreach ($productColors as $colorItem) {
                if (is_array($colorItem)) {
                    $mappedColorsDetail[] = [
                        'name' => $colorItem['name'] ?? 'Custom',
                        'hex' => $colorItem['hex'] ?? '#000000',
                    ];
                } else {
                    $mappedColorsDetail[] = [
                        'name' => $colorsList[$colorItem] ?? 'Custom',
                        'hex' => $colorItem,
                    ];
                }
            }

            $base = array_merge($base, [
                'description' => $this->description,
                'images' => $images->toArray(),
                'colors' => $mappedColorsDetail,
                'sizes' => $productSizes,
                'features' => is_array($this->features) && count($this->features) > 0
                    ? $this->features
                    : [
                        'Moisture-wicking fabric',
                        'Breathable mesh panels',
                        'Reinforced stitching',
                        'Athletic fit',
                        'Quick-dry technology',
                        'Fully customizable',
                    ],
                'reviews_list' => ($this->relationLoaded('reviews') ? $this->reviews : $this->reviews()->latest()->get())->map(fn ($r) => [
                    'id' => $r->id,
                    'reviewer_name' => $r->reviewer_name,
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'date' => $r->created_at->diffForHumans(),
                ])->toArray(),
            ]);
        }

        return $base;
    }
}
