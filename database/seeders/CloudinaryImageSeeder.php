<?php

namespace Database\Seeders;

use App\Models\CloudinaryImage;
use Illuminate\Database\Seeder;

class CloudinaryImageSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            ['name' => 'mountain-landscape.jpg', 'title' => 'Mountain Landscape', 'category' => 'Nature', 'tags' => ['mountain', 'nature'], 'photo' => 'photo-1464822759023-fed622ff2c3b', 'width' => 2400, 'height' => 1600, 'size' => 2840000, 'favorite' => true],
            ['name' => 'ocean-sunset.jpg', 'title' => 'Ocean Sunset', 'category' => 'Nature', 'tags' => ['ocean', 'sunset'], 'photo' => 'photo-1507525428034-b723cf961d3e', 'width' => 2400, 'height' => 1600, 'size' => 2190000, 'favorite' => false],
            ['name' => 'city-street.jpg', 'title' => 'City Street', 'category' => 'Travel', 'tags' => ['city', 'street'], 'photo' => 'photo-1477959858617-67f85cf4f1df', 'width' => 2400, 'height' => 1600, 'size' => 1980000, 'favorite' => true],
            ['name' => 'coffee-table.jpg', 'title' => 'Coffee Table', 'category' => 'Lifestyle', 'tags' => ['coffee', 'lifestyle'], 'photo' => 'photo-1495474472287-4d71bcdd2085', 'width' => 2400, 'height' => 1600, 'size' => 1760000, 'favorite' => false],
            ['name' => 'forest-road.jpg', 'title' => 'Forest Road', 'category' => 'Nature', 'tags' => ['forest', 'road'], 'photo' => 'photo-1448375240586-882707db888b', 'width' => 2400, 'height' => 1600, 'size' => 2630000, 'favorite' => true],
            ['name' => 'architecture.jpg', 'title' => 'Modern Architecture', 'category' => 'Architecture', 'tags' => ['building', 'design'], 'photo' => 'photo-1487958449943-2429e8be8625', 'width' => 2400, 'height' => 1600, 'size' => 2410000, 'favorite' => false],
            ['name' => 'working-desk.jpg', 'title' => 'Creative Workspace', 'category' => 'Work', 'tags' => ['desk', 'workspace'], 'photo' => 'photo-1497366754035-f200968a6e72', 'width' => 2400, 'height' => 1600, 'size' => 1870000, 'favorite' => false],
            ['name' => 'colorful-flowers.jpg', 'title' => 'Colorful Flowers', 'category' => 'Nature', 'tags' => ['flowers', 'color'], 'photo' => 'photo-1490750967868-88aa4486c946', 'width' => 2400, 'height' => 1600, 'size' => 2050000, 'favorite' => true],
            ['name' => 'travel-camera.jpg', 'title' => 'Travel Camera', 'category' => 'Travel', 'tags' => ['camera', 'travel'], 'photo' => 'photo-1516035069371-29a1b244cc32', 'width' => 2400, 'height' => 1600, 'size' => 2260000, 'favorite' => false],
            ['name' => 'dessert.jpg', 'title' => 'Fresh Dessert', 'category' => 'Food', 'tags' => ['food', 'dessert'], 'photo' => 'photo-1551024506-0bccd828d307', 'width' => 2400, 'height' => 1600, 'size' => 1540000, 'favorite' => false],
            ['name' => 'lake-view.jpg', 'title' => 'Lake View', 'category' => 'Nature', 'tags' => ['lake', 'landscape'], 'photo' => 'photo-1500534623283-312aade485b7', 'width' => 2400, 'height' => 1600, 'size' => 2710000, 'favorite' => true],
            ['name' => 'office-chair.jpg', 'title' => 'Office Interior', 'category' => 'Work', 'tags' => ['office', 'interior'], 'photo' => 'photo-1497366811353-6870744d04b2', 'width' => 2400, 'height' => 1600, 'size' => 1920000, 'favorite' => false],
        ];

        foreach ($images as $image) {
            $photo = str_replace(' ', '', $image['photo']);
            $publicId = 'testing/' . pathinfo($image['name'], PATHINFO_FILENAME);

            CloudinaryImage::updateOrCreate(
                ['public_id' => $publicId],
                [
                    'original_name' => $image['name'],
                    'title' => $image['title'],
                    'description' => 'Testing image for the Cloudinary image manager.',
                    'secure_url' => "https://images.unsplash.com/{$photo}?auto=format&fit=crop&w=1200&q=80",
                    'format' => 'jpg',
                    'resource_type' => 'image',
                    'file_size' => $image['size'],
                    'width' => $image['width'],
                    'height' => $image['height'],
                    'folder' => 'testing',
                    'category' => $image['category'],
                    'tags' => $image['tags'],
                    'is_favorite' => $image['favorite'],
                    'content_hash' => hash('sha256', $publicId),
                    'upload_status' => 'completed',
                ]
            );
        }
    }
}