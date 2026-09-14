<?php

namespace App\Http\Controllers;

use App\Models\CloudinaryImage;
use Illuminate\Support\Facades\DB;

class CloudinaryAnalyticsController extends Controller
{
    /**
     * Display Cloudinary analytics dashboard.
     */
    public function index()
    {
        $totalImages = CloudinaryImage::active()->count();

        $totalBytes = CloudinaryImage::active()->sum('file_size');

        $todayUploads = CloudinaryImage::active()->whereDate(
            'created_at',
            today()
        )->count();

        $weekUploads = CloudinaryImage::active()->where(
            'created_at',
            '>=',
            now()->startOfWeek()
        )->count();

        $averageSize = CloudinaryImage::active()->avg('file_size');

        $largestImage = CloudinaryImage::active()->orderByDesc(
            'file_size'
        )->first();

        $formatStatistics = CloudinaryImage::active()->select(
                'format',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('format')
            ->orderByDesc('total')
            ->get();

        $recentImages = CloudinaryImage::active()->latest()
            ->take(10)
            ->get();

        return view(
            'cloudinary-analytics',
            compact(
                'totalImages',
                'totalBytes',
                'todayUploads',
                'weekUploads',
                'averageSize',
                'largestImage',
                'formatStatistics',
                'recentImages'
            )
        );
    }
}