<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class CloudinaryUploadController extends Controller
{
    // Show the image upload form
    public function index()
    {
        return view('cloudinary-upload');
    }

    // Handle image upload request
    public function upload(Request $request)
    {
        // Validate uploaded image
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);

        // Configure Cloudinary credentials
        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true // Use HTTPS URLs
            ]
        ]);

        // Upload image to Cloudinary
        $result = (new UploadApi())->upload(
            $request->file('image')->getRealPath(),
            [
                'folder' => 'laravel_uploads' // Upload folder name
            ]
        );

        // Redirect back with success message and image URL
        return back()
            ->with('success', 'Image uploaded successfully')
            ->with('url', $result['secure_url']);
    }
}
