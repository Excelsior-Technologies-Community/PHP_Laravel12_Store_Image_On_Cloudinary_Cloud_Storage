<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CloudinaryUploadController;
use App\Http\Controllers\CloudinaryAnalyticsController;

Route::get('/', [CloudinaryAnalyticsController::class, 'index'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Cloudinary Image Management
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Gallery + Search + Filters + Sorting
|--------------------------------------------------------------------------
*/

Route::get(
    '/cloudinary-upload',
    [CloudinaryUploadController::class, 'index']
)->name('cloudinary.index');

/*
|--------------------------------------------------------------------------
| Upload Image
|--------------------------------------------------------------------------
*/

Route::post(
    '/cloudinary-upload',
    [CloudinaryUploadController::class, 'upload']
)->name('cloudinary.upload');

Route::post('/cloudinary-image/{image}/metadata', [CloudinaryUploadController::class, 'updateMetadata'])
    ->name('cloudinary.metadata');

Route::post('/cloudinary-image/{image}/favorite', [CloudinaryUploadController::class, 'toggleFavorite'])
    ->name('cloudinary.favorite');

Route::post('/cloudinary-image/{image}/rename', [CloudinaryUploadController::class, 'rename'])
    ->name('cloudinary.rename');

/*
|--------------------------------------------------------------------------
| Image Details
|--------------------------------------------------------------------------
*/

Route::get(
    '/cloudinary-image/{image}',
    [CloudinaryUploadController::class, 'show']
)->name('cloudinary.show');

/*
|--------------------------------------------------------------------------
| Delete Single Image
|--------------------------------------------------------------------------
*/

Route::delete(
    '/cloudinary-image/{image}',
    [CloudinaryUploadController::class, 'destroy']
)->name('cloudinary.destroy');

/*
|--------------------------------------------------------------------------
| Bulk Delete
|--------------------------------------------------------------------------
*/

Route::delete(
    '/cloudinary-images/bulk-delete',
    [CloudinaryUploadController::class, 'bulkDestroy']
)->name('cloudinary.bulkDestroy');

Route::post('/cloudinary-images/zip', [CloudinaryUploadController::class, 'zip'])
    ->name('cloudinary.zip');

Route::get('/cloudinary-recycle-bin', [CloudinaryUploadController::class, 'recycleBin'])
    ->name('cloudinary.recycleBin');

Route::patch('/cloudinary-image/{image}/restore', [CloudinaryUploadController::class, 'restore'])
    ->name('cloudinary.restore');

Route::delete('/cloudinary-image/{image}/permanent', [CloudinaryUploadController::class, 'permanentlyDestroy'])
    ->name('cloudinary.permanentDestroy');

Route::post('/cloudinary-upload/retry/{failedUpload}', [CloudinaryUploadController::class, 'retryUpload'])
    ->name('cloudinary.retry');

/*
|--------------------------------------------------------------------------
| Copy Cloudinary URL
|--------------------------------------------------------------------------
*/

Route::get(
    '/cloudinary-image/{image}/copy-url',
    [CloudinaryUploadController::class, 'copyUrl']
)->name('cloudinary.copyUrl');

/*
|--------------------------------------------------------------------------
| Download Image
|--------------------------------------------------------------------------
*/

Route::get(
    '/cloudinary-image/{image}/download',
    [CloudinaryUploadController::class, 'download']
)->name('cloudinary.download');

/*
|--------------------------------------------------------------------------
| Cloudinary Transformations
|--------------------------------------------------------------------------
*/

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