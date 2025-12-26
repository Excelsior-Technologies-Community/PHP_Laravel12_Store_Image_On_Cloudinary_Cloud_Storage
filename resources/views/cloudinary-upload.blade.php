<!DOCTYPE html>
<html>
<head>
    <title>Cloudinary Image Upload</title> <!-- Page title -->
</head>
<body>

<h2>Cloudinary Image Upload</h2> <!-- Page heading -->

{{-- Show success message and uploaded image --}}
@if(session('success'))
    <p style="color:green">{{ session('success') }}</p> <!-- Success message -->
    <img src="{{ session('url') }}" width="300"> <!-- Display uploaded image -->
@endif

<!-- Image upload form -->
<form method="POST" action="/cloudinary-upload" enctype="multipart/form-data">
    @csrf <!-- CSRF protection -->
    <input type="file" name="image"> <!-- File input -->
    <button type="submit">Upload</button> <!-- Submit button -->
</form>

</body>
</html>
