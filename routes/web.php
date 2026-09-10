<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CloudinaryUploadController;
use App\Http\Controllers\CloudinaryAnalyticsController;

/*
|--------------------------------------------------------------------------
| Cloudinary Image Management
|--------------------------------------------------------------------------
*/

// Upload page
Route::get(
    '/cloudinary-upload',
    [CloudinaryUploadController::class, 'index']
)->name('cloudinary.index');

// Upload image
Route::post(
    '/cloudinary-upload',
    [CloudinaryUploadController::class, 'upload']
)->name('cloudinary.upload');

// Image details
Route::get(
    '/cloudinary-image/{image}',
    [CloudinaryUploadController::class, 'show']
)->name('cloudinary.show');

// Delete image
Route::delete(
    '/cloudinary-image/{image}',
    [CloudinaryUploadController::class, 'destroy']
)->name('cloudinary.destroy');

// Cloudinary transformations
Route::get(
    '/cloudinary-image/{image}/transform/{transformation}',
    [CloudinaryUploadController::class, 'transform']
)->name('cloudinary.transform');

/*
|--------------------------------------------------------------------------
| Cloudinary Analytics
|--------------------------------------------------------------------------
*/

Route::get(
    '/cloudinary-analytics',
    [CloudinaryAnalyticsController::class, 'index']
)->name('cloudinary.analytics');