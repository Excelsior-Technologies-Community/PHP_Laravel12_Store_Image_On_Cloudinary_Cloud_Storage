<?php

namespace App\Http\Controllers;

use App\Models\CloudinaryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
     * Display upload page with search, filters and sorting.
     */
    public function index(Request $request)
    {
        $query = CloudinaryImage::query();

        /*
        |--------------------------------------------------------------------------
        | 1. Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('original_name', 'like', '%' . $search . '%')
                    ->orWhere('public_id', 'like', '%' . $search . '%');

            });
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Format Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('format')) {

            $query->where(
                'format',
                strtolower($request->format)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Date Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_filter')) {

            switch ($request->date_filter) {

                case 'today':

                    $query->whereDate(
                        'created_at',
                        today()
                    );

                    break;

                case 'week':

                    $query->where(
                        'created_at',
                        '>=',
                        now()->startOfWeek()
                    );

                    break;

                case 'month':

                    $query->where(
                        'created_at',
                        '>=',
                        now()->startOfMonth()
                    );

                    break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Minimum File Size
        |--------------------------------------------------------------------------
        | Input is in KB.
        |--------------------------------------------------------------------------
        */

        if ($request->filled('min_size')) {

            $minSize = (float) $request->min_size;

            if ($minSize >= 0) {

                $query->where(
                    'file_size',
                    '>=',
                    $minSize * 1024
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Maximum File Size
        |--------------------------------------------------------------------------
        | Input is in KB.
        |--------------------------------------------------------------------------
        */

        if ($request->filled('max_size')) {

            $maxSize = (float) $request->max_size;

            if ($maxSize >= 0) {

                $query->where(
                    'file_size',
                    '<=',
                    $maxSize * 1024
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Sorting
        |--------------------------------------------------------------------------
        */

        $sort = $request->get('sort', 'newest');

        switch ($sort) {

            case 'oldest':

                $query->orderBy(
                    'created_at',
                    'asc'
                );

                break;

            case 'largest':

                $query->orderBy(
                    'file_size',
                    'desc'
                );

                break;

            case 'smallest':

                $query->orderBy(
                    'file_size',
                    'asc'
                );

                break;

            case 'name_asc':

                $query->orderBy(
                    'original_name',
                    'asc'
                );

                break;

            case 'name_desc':

                $query->orderBy(
                    'original_name',
                    'desc'
                );

                break;

            default:

                $query->latest();

                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $images = $query
            ->paginate(8)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Gallery statistics
        |--------------------------------------------------------------------------
        */

        $totalImages = CloudinaryImage::count();

        $totalStorage = CloudinaryImage::sum('file_size');

        $filteredImages = $images->total();

        /*
        |--------------------------------------------------------------------------
        | Available formats
        |--------------------------------------------------------------------------
        */

        $formats = CloudinaryImage::query()
            ->whereNotNull('format')
            ->select('format')
            ->distinct()
            ->orderBy('format')
            ->pluck('format');

        return view(
            'cloudinary-upload',
            compact(
                'images',
                'totalImages',
                'totalStorage',
                'filteredImages',
                'formats'
            )
        );
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
            'format' => $result['format']
                ?? $file->getClientOriginalExtension(),
            'resource_type' => $result['resource_type']
                ?? 'image',
            'file_size' => $result['bytes']
                ?? $file->getSize(),
            'width' => $result['width']
                ?? null,
            'height' => $result['height']
                ?? null,
            'folder' => 'laravel_uploads',
        ]);

        return redirect()
            ->route('cloudinary.index')
            ->with(
                'success',
                'Image uploaded successfully to Cloudinary.'
            );
    }

    /**
     * Display image details.
     */
    public function show(CloudinaryImage $image)
    {
        return view(
            'cloudinary-show',
            compact('image')
        );
    }

    /**
     * Delete single image from Cloudinary and database.
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

            Log::error(
                'Cloudinary single image deletion failed',
                [
                    'image_id' => $image->id,
                    'public_id' => $image->public_id,
                    'error' => $e->getMessage(),
                ]
            );

            return redirect()
                ->route('cloudinary.index')
                ->with(
                    'error',
                    'Unable to delete image: ' . $e->getMessage()
                );
        }
    }

    /**
     * Bulk delete selected images.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'images' => [
                'required',
                'array',
                'min:1',
            ],

            'images.*' => [
                'integer',
                'exists:cloudinary_images,id',
            ],
        ]);

        $this->configureCloudinary();

        $deleted = 0;
        $failed = 0;

        $images = CloudinaryImage::whereIn(
            'id',
            $request->images
        )->get();

        foreach ($images as $image) {

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

                $deleted++;

            } catch (\Throwable $e) {

                $failed++;

                Log::error(
                    'Cloudinary bulk deletion failed',
                    [
                        'image_id' => $image->id,
                        'public_id' => $image->public_id,
                        'error' => $e->getMessage(),
                    ]
                );
            }
        }

        if ($failed > 0) {

            return redirect()
                ->route('cloudinary.index')
                ->with(
                    'error',
                    $deleted . ' image(s) deleted, but '
                    . $failed . ' image(s) could not be deleted.'
                );
        }

        return redirect()
            ->route('cloudinary.index')
            ->with(
                'success',
                $deleted . ' image(s) deleted successfully.'
            );
    }

    /**
     * Copyable Cloudinary URL response.
     */
    public function copyUrl(CloudinaryImage $image)
    {
        return response()->json([
            'success' => true,
            'url' => $image->secure_url,
        ]);
    }

    /**
     * Download original Cloudinary image.
     */
    public function download(CloudinaryImage $image)
    {
        $cloudName = config(
            'cloudinary.cloud_name'
        );

        $publicId = ltrim(
            $image->public_id,
            '/'
        );

        /*
        |--------------------------------------------------------------------------
        | Cloudinary attachment URL
        |--------------------------------------------------------------------------
        */

        $url = sprintf(
            'https://res.cloudinary.com/%s/image/upload/fl_attachment/%s',
            $cloudName,
            $publicId
        );

        return redirect()->away($url);
    }

    /**
     * Generate Cloudinary transformation URL.
     */
    public function transform(
        CloudinaryImage $image,
        string $transformation
    ) {
        $allowedTransformations = [

            'thumbnail' =>
                'w_300,h_200,c_fill,q_auto,f_auto',

            'small' =>
                'w_500,h_500,c_limit,q_auto,f_auto',

            'medium' =>
                'w_800,h_800,c_limit,q_auto,f_auto',

            'square' =>
                'w_500,h_500,c_fill,g_auto,q_auto,f_auto',

            'optimized' =>
                'q_auto,f_auto',
        ];

        if (
            !array_key_exists(
                $transformation,
                $allowedTransformations
            )
        ) {
            abort(404);
        }

        $cloudName = config(
            'cloudinary.cloud_name'
        );

        $transformationString =
            $allowedTransformations[$transformation];

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