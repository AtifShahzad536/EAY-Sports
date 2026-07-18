<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class UpdateExistingProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $availableColors = ['#1D4ED8', '#DC2626', '#16A34A', '#FFFFFF', '#1e293b', '#374151', '#4F46E5'];
        $availableSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size'];

        $products = Product::all();

        $this->command->info('Updating products with default colors and sizes (skipping manually configured ones)...');

        $updatedCount = 0;

        foreach ($products as $product) {
            // Only update if colors or sizes is empty/null
            $needsColors = empty($product->colors);
            $needsSizes = empty($product->sizes);

            if ($needsColors || $needsSizes) {
                // Pick a random number of colors (between 2 and 5)
                $colorsCount = rand(2, 5);
                $randomColorKeys = array_rand($availableColors, $colorsCount);

                // Pick a random number of sizes (between 3 and 6)
                $sizesCount = rand(3, 6);
                $randomSizeKeys = array_rand($availableSizes, $sizesCount);

                // Map keys to values
                $selectedColors = [];
                if (is_array($randomColorKeys)) {
                    foreach ($randomColorKeys as $key) {
                        $selectedColors[] = $availableColors[$key];
                    }
                } else {
                    $selectedColors[] = $availableColors[$randomColorKeys];
                }

                $selectedSizes = [];
                if (is_array($randomSizeKeys)) {
                    foreach ($randomSizeKeys as $key) {
                        $selectedSizes[] = $availableSizes[$key];
                    }
                } else {
                    $selectedSizes[] = $availableSizes[$randomSizeKeys];
                }

                // Save to database, maintaining existing configuration if any
                $product->update([
                    'colors' => $needsColors ? $selectedColors : $product->colors,
                    'sizes' => $needsSizes ? $selectedSizes : $product->sizes,
                ]);

                $updatedCount++;
            }
        }

        $this->command->info("Completed! Updated {$updatedCount} products.");
    }
}
