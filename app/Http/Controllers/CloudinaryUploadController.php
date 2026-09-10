<?php

namespace App\Http\Controllers;

use App\Models\CloudinaryImage;
use Illuminate\Http\Request;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class CloudinaryUploadController extends Controller
{
    /**
     * Configure Cloudinary.
     */
    private function configureCloudinary(): void
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    /**
     * Display upload page.
     */
    public function index()
    {
        $images = CloudinaryImage::latest()->paginate(8);

        return view('cloudinary-upload', compact('images'));
    }

    /**
     * Upload image to Cloudinary.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:5120',
            ],
        ]);

        $this->configureCloudinary();

        $file = $request->file('image');

        $result = (new UploadApi())->upload(
            $file->getRealPath(),
            [
                'folder' => 'laravel_uploads',
                'resource_type' => 'image',
                'use_filename' => true,
                'unique_filename' => true,
            ]
        );

        CloudinaryImage::create([
            'original_name' => $file->getClientOriginalName(),
            'public_id' => $result['public_id'],
            'secure_url' => $result['secure_url'],
            'format' => $result['format'] ?? $file->getClientOriginalExtension(),
            'resource_type' => $result['resource_type'] ?? 'image',
            'file_size' => $result['bytes'] ?? $file->getSize(),
            'width' => $result['width'] ?? null,
            'height' => $result['height'] ?? null,
            'folder' => 'laravel_uploads',
        ]);

        return redirect()
            ->route('cloudinary.index')
            ->with('success', 'Image uploaded successfully to Cloudinary.');
    }

    /**
     * Display image details.
     */
    public function show(CloudinaryImage $image)
    {
        return view('cloudinary-show', compact('image'));
    }

    /**
     * Delete image from Cloudinary and database.
     */
    public function destroy(CloudinaryImage $image)
    {
        $this->configureCloudinary();

        try {
            (new UploadApi())->destroy(
                $image->public_id,
                [
                    'resource_type' => 'image',
                    'type' => 'upload',
                    'invalidate' => true,
                ]
            );

            $image->delete();

            return redirect()
                ->route('cloudinary.index')
                ->with(
                    'success',
                    'Image deleted successfully from Cloudinary and database.'
                );
        } catch (\Throwable $e) {
            return redirect()
                ->route('cloudinary.index')
                ->with(
                    'error',
                    'Unable to delete image: ' . $e->getMessage()
                );
        }
    }

    /**
     * Generate Cloudinary transformation URL.
     */
    public function transform(
        CloudinaryImage $image,
        string $transformation
    ) {
        $allowedTransformations = [
            'thumbnail' => 'w_300,h_200,c_fill,q_auto,f_auto',
            'small' => 'w_500,h_500,c_limit,q_auto,f_auto',
            'medium' => 'w_800,h_800,c_limit,q_auto,f_auto',
            'square' => 'w_500,h_500,c_fill,g_auto,q_auto,f_auto',
            'optimized' => 'q_auto,f_auto',
        ];

        if (!array_key_exists($transformation, $allowedTransformations)) {
            abort(404);
        }

        $cloudName = config('cloudinary.cloud_name');

        $transformationString = $allowedTransformations[$transformation];

        $url = sprintf(
            'https://res.cloudinary.com/%s/image/upload/%s/%s',
            $cloudName,
            $transformationString,
            $image->public_id
        );

        return view(
            'cloudinary-transform',
            compact(
                'image',
                'url',
                'transformation'
            )
        );
    }
}