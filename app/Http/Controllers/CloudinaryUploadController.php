<?php

namespace App\Http\Controllers;

use App\Models\CloudinaryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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
        $query = CloudinaryImage::active();

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

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('favorites')) {
            $query->where('is_favorite', true);
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', trim($request->tag));
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

        $totalImages = CloudinaryImage::active()->count();

        $totalStorage = CloudinaryImage::active()->sum('file_size');

        $filteredImages = $images->total();

        /*
        |--------------------------------------------------------------------------
        | Available formats
        |--------------------------------------------------------------------------
        */

        $formats = CloudinaryImage::active()
            ->whereNotNull('format')
            ->select('format')
            ->distinct()
            ->orderBy('format')
            ->pluck('format');

        $categories = CloudinaryImage::active()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $trashedCount = CloudinaryImage::trashed()->count();
        $failedUploads = DB::table('failed_cloudinary_uploads')->latest()->get();

        return view(
            'cloudinary-upload',
            compact(
                'images',
                'totalImages',
                'totalStorage',
                'filteredImages',
                'formats'
                , 'categories', 'trashedCount', 'failedUploads'
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
                'mimes:jpg,jpeg,png,gif,webp,avif',
                'max:5120',
            ],
        ]);

        $file = $request->file('image');
        $hash = hash_file('sha256', $file->getRealPath());

        if (CloudinaryImage::active()->where('content_hash', $hash)->exists()) {
            return back()->with('error', 'Duplicate image detected. This image is already uploaded.');
        }

        $metadata = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'category' => $request->input('category'),
            'tags' => $this->normalizeTags($request->input('tags')),
        ];

        $this->configureCloudinary();

        try {
            $result = (new UploadApi())->upload($file->getRealPath(), [
                'folder' => $metadata['category'] ?: 'laravel_uploads',
                'resource_type' => 'image',
                'use_filename' => true,
                'unique_filename' => true,
                'tags' => $metadata['tags'],
                'context' => array_filter([
                    'title' => $metadata['title'],
                    'description' => $metadata['description'],
                ]),
                'phash' => true,
                'backup' => true,
            ]);
        } catch (\Throwable $e) {
            $path = $file->store('cloudinary-retries');
            DB::table('failed_cloudinary_uploads')->insert([
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'title' => $metadata['title'],
                'description' => $metadata['description'],
                'category' => $metadata['category'],
                'tags' => json_encode($metadata['tags']),
                'failure_message' => $e->getMessage(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::error('Cloudinary upload failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Upload failed. It has been saved for retry.');
        }

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
            'title' => $metadata['title'],
            'description' => $metadata['description'],
            'category' => $metadata['category'],
            'tags' => $metadata['tags'],
            'content_hash' => $hash,
            'upload_status' => 'completed',
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
        $image->update(['deleted_at' => now()]);
        return redirect()->route('cloudinary.index')->with('success', 'Image moved to recycle bin.');
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

        $deleted = 0;
        $failed = 0;

        $images = CloudinaryImage::whereIn(
            'id',
            $request->images
        )->get();

        foreach ($images as $image) {
            $image->update(['deleted_at' => now()]);
            $deleted++;
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
        string $transformation,
        Request $request
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

        $parts = [$allowedTransformations[$transformation]];
        $custom = array_filter([
            $request->integer('width') > 0 ? 'w_' . min($request->integer('width'), 4000) : null,
            $request->integer('height') > 0 ? 'h_' . min($request->integer('height'), 4000) : null,
            in_array($request->input('crop'), ['fill', 'fit', 'limit', 'thumb', 'crop'], true) ? 'c_' . $request->input('crop') : null,
            in_array($request->input('rotate'), ['90', '180', '270'], true) ? 'a_' . $request->input('rotate') : null,
            $request->boolean('flip_h') ? 'fl_h' : null,
            $request->boolean('flip_v') ? 'fl_v' : null,
            $request->integer('quality') >= 1 && $request->integer('quality') <= 100 ? 'q_' . $request->integer('quality') : null,
            in_array($request->input('format'), ['jpg', 'png', 'webp', 'avif'], true) ? 'f_' . $request->input('format') : null,
            $request->integer('blur') >= 1 && $request->integer('blur') <= 2000 ? 'e_blur:' . $request->integer('blur') : null,
            $request->boolean('sharpen') ? 'e_sharpen' : null,
            $request->boolean('watermark') ? 'l_text:Arial_24:Cloudinary%20Manager,co_white,o_60' : null,
            $request->boolean('remove_background') ? 'e_background_removal' : null,
        ]);
        if ($custom) {
            $parts[] = implode(',', $custom);
        }

        $cloudName = config(
            'cloudinary.cloud_name'
        );

        $transformationString = implode('/', $parts);

        $url = sprintf(
            'https://res.cloudinary.com/%s/image/upload/%s/%s',
            $cloudName,
            $transformationString,
            $image->public_id
        );

        $responsiveUrls = [];
        foreach ([320, 640, 960, 1280] as $width) {
            $responsiveUrls[$width] = sprintf(
                'https://res.cloudinary.com/%s/image/upload/w_%d,c_limit,q_auto,f_auto/%s',
                $cloudName,
                $width,
                $image->public_id
            );
        }

        return view(
            'cloudinary-transform',
            compact(
                'image',
                'url',
                'transformation'
                , 'responsiveUrls'
            )
        );
    }

    public function updateMetadata(Request $request, CloudinaryImage $image)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:1000',
        ]);
        $data['tags'] = $this->normalizeTags($data['tags'] ?? null);
        $image->update($data);
        return back()->with('success', 'Image information updated successfully.');
    }

    public function toggleFavorite(CloudinaryImage $image)
    {
        $image->update(['is_favorite' => !$image->is_favorite]);
        return back()->with('success', $image->is_favorite ? 'Added to favorites.' : 'Removed from favorites.');
    }

    public function rename(Request $request, CloudinaryImage $image)
    {
        $data = $request->validate(['public_id' => 'required|string|max:255']);
        $this->configureCloudinary();
        (new UploadApi())->rename($image->public_id, trim($data['public_id']), ['overwrite' => false]);
        $image->update(['public_id' => trim($data['public_id'])]);
        return back()->with('success', 'Image renamed successfully.');
    }

    public function recycleBin()
    {
        $images = CloudinaryImage::trashed()->latest()->paginate(12);
        return view('cloudinary-recycle-bin', compact('images'));
    }

    public function restore(CloudinaryImage $image)
    {
        $image->update(['deleted_at' => null]);
        return back()->with('success', 'Image restored successfully.');
    }

    public function permanentlyDestroy(CloudinaryImage $image)
    {
        $this->configureCloudinary();
        (new UploadApi())->destroy($image->public_id, ['resource_type' => 'image', 'type' => 'upload', 'invalidate' => true]);
        $image->delete();
        return back()->with('success', 'Image permanently deleted.');
    }

    public function zip(Request $request)
    {
        $request->validate(['images' => 'required|array|min:1', 'images.*' => 'integer|exists:cloudinary_images,id']);
        $this->configureCloudinary();
        $publicIds = CloudinaryImage::active()->whereIn('id', $request->images)->pluck('public_id')->all();
        $url = (new UploadApi())->downloadArchiveUrl(['public_ids' => $publicIds, 'resource_type' => 'image', 'target_format' => 'zip']);
        return redirect()->away($url);
    }

    public function retryUpload(int $failedUpload)
    {
        $upload = DB::table('failed_cloudinary_uploads')->where('id', $failedUpload)->firstOrFail();
        if (!Storage::exists($upload->file_path)) {
            return back()->with('error', 'Retry file is no longer available.');
        }
        $this->configureCloudinary();
        try {
            $result = (new UploadApi())->upload(Storage::path($upload->file_path), ['folder' => $upload->category ?: 'laravel_uploads', 'resource_type' => 'image', 'use_filename' => true, 'unique_filename' => true, 'tags' => json_decode($upload->tags, true) ?: []]);
            CloudinaryImage::create(['original_name' => $upload->original_name, 'title' => $upload->title, 'description' => $upload->description, 'category' => $upload->category, 'tags' => json_decode($upload->tags, true), 'public_id' => $result['public_id'], 'secure_url' => $result['secure_url'], 'format' => $result['format'] ?? null, 'resource_type' => $result['resource_type'] ?? 'image', 'file_size' => $result['bytes'] ?? 0, 'width' => $result['width'] ?? null, 'height' => $result['height'] ?? null, 'folder' => $upload->category ?: 'laravel_uploads']);
            Storage::delete($upload->file_path);
            DB::table('failed_cloudinary_uploads')->where('id', $failedUpload)->delete();
            return back()->with('success', 'Failed upload retried successfully.');
        } catch (\Throwable $e) {
            DB::table('failed_cloudinary_uploads')->where('id', $failedUpload)->update(['retry_count' => $upload->retry_count + 1, 'failure_message' => $e->getMessage(), 'updated_at' => now()]);
            return back()->with('error', 'Retry failed: ' . $e->getMessage());
        }
    }

    private function normalizeTags(?string $tags): array
    {
        return collect(preg_split('/[,\s]+/', (string) $tags))->map(fn ($tag) => trim($tag))->filter()->unique()->values()->all();
    }
}