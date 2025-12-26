<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CloudinaryUploadController;

// Show Cloudinary upload form
Route::get('/cloudinary-upload', [CloudinaryUploadController::class, 'index']);

// Handle Cloudinary image upload
Route::post('/cloudinary-upload', [CloudinaryUploadController::class, 'upload']);
