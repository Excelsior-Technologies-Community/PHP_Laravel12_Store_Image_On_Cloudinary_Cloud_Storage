# PHP_Laravel12_Store_Image_On_Cloudinary_Cloud_Storage

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/PHP-8%2B-777BB4?style=for-the-badge&logo=php">
  <img src="https://img.shields.io/badge/Cloudinary-Cloud%20Storage-blue?style=for-the-badge">
  <img src="https://img.shields.io/badge/Image-Upload-success?style=for-the-badge">
</p>

---

##  Overview

This project demonstrates how to store images in Cloudinary Cloud Storage
from a Laravel 12 application using a real-world, production-ready approach.

Users upload an image using a Blade form, and the image is securely stored in
Cloudinary Cloud Storage. After successful upload, the secure image URL returned
by Cloudinary is displayed on the browser.

---

##  Features

- Laravel 12
- Cloudinary Cloud Storage integration
- Secure API credentials
- Blade-based image upload form
- Image validation
- Upload directly to cloud (no local storage)
- Display uploaded image preview

---

##  Folder Structure

```text
cloudinary-demo/
│
├── app/
│   └── Http/
│       └── Controllers/
│           └── CloudinaryUploadController.php
│
├── config/
│   └── cloudinary.php
│
├── resources/
│   └── views/
│       └── cloudinary-upload.blade.php
│
├── routes/
│   └── web.php
│
├── .env
├── composer.json
└── README.md
```

---

## STEP 1: Create New Laravel Project

```bash
composer create-project laravel/laravel cloudinary-demo
```

---

## STEP 2: .env FILE 

```env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

```bash
php artisan config:clear
php artisan config:cache
```

---

## STEP 3: Run Laravel Project

```bash
php artisan serve
```

---


## STEP 4: Create FREE Cloudinary Account

Visit:

https://cloudinary.com/users/register/free

Signup using Google / Email  

<img width="1094" height="654" alt="Screenshot 2025-12-26 133004" src="https://github.com/user-attachments/assets/2249c51e-2523-4a28-a156-7b33bb66ee57" />

No credit card required  

Login to Cloudinary Dashboard  

---

## STEP 5: Get Cloudinary API Credentials

From Cloudinary Dashboard  
Click **View API Keys**  

<img width="1905" height="944" alt="Screenshot 2025-12-26 133144" src="https://github.com/user-attachments/assets/56ec2f48-aa0e-4120-b047-7dac95875aca" />

Copy:

Cloud Name  
API Key  
API Secret  

⚠️ **Keep API Secret private**

## Add credentials in .env
```
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```


## STEP 6: Install Cloudinary PHP SDK
 Install Cloudinary PHP SDK

```bash
composer require cloudinary/cloudinary_php
```

---

## STEP 7: Cloudinary Configuration

### config/cloudinary.php

```php
<?php

return [
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key'    => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
];
```

---

## STEP 8: Create Upload Controller

```bash
php artisan make:controller CloudinaryUploadController
```

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class CloudinaryUploadController extends Controller
{
    public function index()
    {
        return view('cloudinary-upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);

        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => ['secure' => true]
        ]);

        $result = (new UploadApi())->upload(
            $request->file('image')->getRealPath(),
            ['folder' => 'laravel_uploads']
        );

        return back()
            ->with('success', 'Image uploaded successfully')
            ->with('url', $result['secure_url']);
    }
}
```

---

## STEP 9: Routes

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CloudinaryUploadController;

Route::get('/cloudinary-upload', [CloudinaryUploadController::class, 'index']);
Route::post('/cloudinary-upload', [CloudinaryUploadController::class, 'upload']);
```

---

## STEP 10: Blade View

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Cloudinary Image Upload</title>
</head>
<body>

<h2>Cloudinary Image Upload</h2>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
    <img src="{{ session('url') }}" width="300">
@endif

<form method="POST" action="/cloudinary-upload" enctype="multipart/form-data">
    @csrf
    <input type="file" name="image">
    <button type="submit">Upload</button>
</form>

</body>
</html>
```

---

## STEP 11: Test

```bash
php artisan serve
```

http://127.0.0.1:8000/cloudinary-upload

---

## FINAL OUTPUT

- Image uploaded successfully

 <img width="489" height="308" alt="Screenshot 2025-12-26 141632" src="https://github.com/user-attachments/assets/7896d35d-1706-4c6d-a157-49d7b257f023" />

- Image stored in Cloudinary Cloud Storage

 <img width="1919" height="944" alt="Screenshot 2025-12-26 132548" src="https://github.com/user-attachments/assets/9c19ddcd-48ef-4458-a196-f26d03fe7a42" />


---

