<?php

namespace Database\Seeders;

use App\Models\BuilderPattern;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BuilderPatternSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure storage directory exists
        if (! Storage::disk('public')->exists('builder_patterns')) {
            Storage::disk('public')->makeDirectory('builder_patterns');
        }

        $basePath = storage_path('app/public/builder_patterns/');
        if (! is_dir($basePath)) {
            mkdir($basePath, 0755, true);
        }

        $stripesPath = 'builder_patterns/stripes.png';
        $dotsPath = 'builder_patterns/dots.png';
        $checkerPath = 'builder_patterns/checker.png';
        $gridPath = 'builder_patterns/grid.png';

        // Generate 1: Vertical Stripes (using WHITE foreground for Three.js dynamic color tinting)
        $img = imagecreatetruecolor(128, 128);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $transparent);
        imagefilledrectangle($img, 0, 0, 31, 127, $white);
        imagefilledrectangle($img, 64, 0, 95, 127, $white);
        imagepng($img, $basePath.'stripes.png');
        imagedestroy($img);

        // Generate 2: Polka Dots
        $img = imagecreatetruecolor(128, 128);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $transparent);
        imagefilledellipse($img, 64, 64, 36, 36, $white);
        imagefilledellipse($img, 0, 0, 36, 36, $white);
        imagefilledellipse($img, 128, 0, 36, 36, $white);
        imagefilledellipse($img, 0, 128, 36, 36, $white);
        imagefilledellipse($img, 128, 128, 36, 36, $white);
        imagepng($img, $basePath.'dots.png');
        imagedestroy($img);

        // Generate 3: Checkerboard
        $img = imagecreatetruecolor(128, 128);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $transparent);
        imagefilledrectangle($img, 0, 0, 63, 63, $white);
        imagefilledrectangle($img, 64, 64, 127, 127, $white);
        imagepng($img, $basePath.'checker.png');
        imagedestroy($img);

        // Generate 4: Grid Pattern
        $img = imagecreatetruecolor(128, 128);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $transparent);
        imageline($img, 0, 0, 127, 0, $white);
        imageline($img, 0, 127, 127, 127, $white);
        imageline($img, 0, 0, 0, 127, $white);
        imageline($img, 127, 0, 127, 127, $white);
        imageline($img, 64, 0, 64, 127, $white);
        imageline($img, 0, 64, 127, 64, $white);
        imagepng($img, $basePath.'grid.png');
        imagedestroy($img);

        // Save records to database
        BuilderPattern::updateOrCreate([
            'name' => 'Vertical Stripes',
        ], [
            'image_path' => '/storage/'.$stripesPath,
            'status' => true,
        ]);

        BuilderPattern::updateOrCreate([
            'name' => 'Polka Dots',
        ], [
            'image_path' => '/storage/'.$dotsPath,
            'status' => true,
        ]);

        BuilderPattern::updateOrCreate([
            'name' => 'Checkerboard',
        ], [
            'image_path' => '/storage/'.$checkerPath,
            'status' => true,
        ]);

        BuilderPattern::updateOrCreate([
            'name' => 'Grid Lines',
        ], [
            'image_path' => '/storage/'.$gridPath,
            'status' => true,
        ]);
    }
}
