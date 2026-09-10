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
        $totalImages = CloudinaryImage::count();

        $totalBytes = CloudinaryImage::sum('file_size');

        $todayUploads = CloudinaryImage::whereDate(
            'created_at',
            today()
        )->count();

        $weekUploads = CloudinaryImage::where(
            'created_at',
            '>=',
            now()->startOfWeek()
        )->count();

        $averageSize = CloudinaryImage::avg('file_size');

        $largestImage = CloudinaryImage::orderByDesc(
            'file_size'
        )->first();

        $formatStatistics = CloudinaryImage::select(
                'format',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('format')
            ->orderByDesc('total')
            ->get();

        $recentImages = CloudinaryImage::latest()
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