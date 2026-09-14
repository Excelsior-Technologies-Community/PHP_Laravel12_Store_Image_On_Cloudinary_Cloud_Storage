<!DOCTYPE html>

<html lang="en">

<head>

```
<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Image Details - Cloudinary
</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<style>

    body {
        background: #f4f6f9;
    }

    .navbar-brand {
        font-weight: 700;
    }

    .detail-card {
        border: 0;
        border-radius: 18px;
    }

    .preview-wrapper {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 15px;
        min-height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .preview {
        width: 100%;
        max-height: 550px;
        object-fit: contain;
        border-radius: 12px;
    }

    .info-card {
        border-radius: 12px;
        overflow: hidden;
    }

    .info-card th {
        width: 38%;
        background: #f8f9fa;
    }

    .public-id {
        word-break: break-all;
    }

    .action-card {
        border-radius: 12px;
    }

    .url-box {
        background: #212529;
        color: #fff;
        padding: 12px;
        border-radius: 8px;
        word-break: break-all;
        font-size: 13px;
    }

    .copy-success {
        background: #198754 !important;
        color: #fff !important;
    }

    .metadata-badge {
        font-size: 13px;
        padding: 7px 10px;
    }

    @media (max-width: 767.98px) {

        .preview-wrapper {
            min-height: 300px;
        }

    }

</style>
```

</head>

<body>

{{-- =========================================================
NAVIGATION
========================================================= --}}

<nav class="navbar navbar-dark bg-dark mb-4">

```
<div class="container">

    <a
        href="{{ route('cloudinary.index') }}"
        class="navbar-brand"
    >
        ☁️ Cloudinary Manager
    </a>

    <div class="d-flex gap-2">

        <a
            href="{{ route('cloudinary.index') }}"
            class="btn btn-outline-light"
        >
            🖼️ Gallery
        </a>

        <a
            href="{{ route('cloudinary.analytics') }}"
            class="btn btn-outline-light"
        >
            📊 Analytics
        </a>

    </div>

</div>
```

</nav>

<div class="container pb-5">

{{-- =========================================================
PAGE HEADER
========================================================= --}}

<div class="d-flex justify-content-between align-items-center mb-4">

```
<div>

    <h2 class="mb-1">
        🖼️ Image Details
    </h2>

    <p class="text-muted mb-0">
        View Cloudinary image information and available actions.
    </p>

</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm"><div class="card-body">
            <h5>📝 Image title, description, category and tags</h5>
            <form method="POST" action="{{ route('cloudinary.metadata', $image) }}" class="row g-2">
                @csrf
                <div class="col-md-6"><input name="title" value="{{ $image->title }}" class="form-control" placeholder="Title"></div>
                <div class="col-md-6"><input name="category" value="{{ $image->category }}" class="form-control" placeholder="Folder / category"></div>
                <div class="col-md-6"><input name="tags" value="{{ implode(', ', $image->tags ?? []) }}" class="form-control" placeholder="Tags"></div>
                <div class="col-md-6"><textarea name="description" class="form-control" placeholder="Description">{{ $image->description }}</textarea></div>
                <div class="col-12"><button class="btn btn-primary">Save Information</button></div>
            </form>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm"><div class="card-body">
            <h5>Asset controls</h5>
            <form method="POST" action="{{ route('cloudinary.favorite', $image) }}" class="mb-3">@csrf<button class="btn btn-warning w-100">{{ $image->is_favorite ? '★ Remove Favorite' : '☆ Add Favorite' }}</button></form>
            <form method="POST" action="{{ route('cloudinary.rename', $image) }}">@csrf<div class="input-group"><input name="public_id" value="{{ $image->public_id }}" class="form-control" required><button class="btn btn-outline-secondary">Rename</button></div></form>
        </div></div>
    </div>
</div>

<a
    href="{{ route('cloudinary.index') }}"
    class="btn btn-secondary"
>
    ← Back to Gallery
</a>
```

</div>

{{-- =========================================================
MAIN CARD
========================================================= --}}

<div class="card shadow-sm detail-card">

```
<div class="card-body p-4">

    <div class="row g-4">


        {{-- =================================================
             IMAGE PREVIEW
        ================================================== --}}

        <div class="col-lg-7">

            <div class="preview-wrapper">

                <img
                    src="{{ $image->secure_url }}"
                    alt="{{ $image->original_name }}"
                    class="preview"
                >

            </div>


            {{-- =================================================
                 QUICK METADATA
            ================================================== --}}

            <div class="d-flex flex-wrap gap-2 mt-3">

                <span class="badge bg-primary metadata-badge">

                    {{ strtoupper($image->format) }}

                </span>


                <span class="badge bg-success metadata-badge">

                    {{ $image->formatted_size }}

                </span>


                <span class="badge bg-secondary metadata-badge">

                    {{ $image->dimensions }}

                </span>


                <span class="badge bg-dark metadata-badge">

                    {{ $image->resource_type }}

                </span>

            </div>

        </div>


        {{-- =================================================
             IMAGE INFORMATION
        ================================================== --}}

        <div class="col-lg-5">


            <div class="card border info-card">

                <div class="card-header bg-dark text-white">

                    <strong>
                        📋 Image Information
                    </strong>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered mb-0">


                            {{-- Original Name --}}

                            <tr>

                                <th>
                                    Original Name
                                </th>

                                <td>

                                    {{ $image->original_name }}

                                </td>

                            </tr>


                            {{-- Format --}}

                            <tr>

                                <th>
                                    Format
                                </th>

                                <td>

                                    <span class="badge bg-primary">

                                        {{ strtoupper($image->format) }}

                                    </span>

                                </td>

                            </tr>


                            {{-- File Size --}}

                            <tr>

                                <th>
                                    File Size
                                </th>

                                <td>

                                    {{ $image->formatted_size }}

                                </td>

                            </tr>


                            {{-- Dimensions --}}

                            <tr>

                                <th>
                                    Dimensions
                                </th>

                                <td>

                                    {{ $image->dimensions }}

                                </td>

                            </tr>


                            {{-- Width --}}

                            <tr>

                                <th>
                                    Width
                                </th>

                                <td>

                                    {{ $image->width ?? 'N/A' }}

                                    @if($image->width)
                                        px
                                    @endif

                                </td>

                            </tr>


                            {{-- Height --}}

                            <tr>

                                <th>
                                    Height
                                </th>

                                <td>

                                    {{ $image->height ?? 'N/A' }}

                                    @if($image->height)
                                        px
                                    @endif

                                </td>

                            </tr>


                            {{-- Resource Type --}}

                            <tr>

                                <th>
                                    Resource Type
                                </th>

                                <td>

                                    {{ $image->resource_type }}

                                </td>

                            </tr>


                            {{-- Folder --}}

                            <tr>

                                <th>
                                    Cloudinary Folder
                                </th>

                                <td>

                                    {{ $image->folder }}

                                </td>

                            </tr>


                            {{-- Public ID --}}

                            <tr>

                                <th>
                                    Public ID
                                </th>

                                <td>

                                    <small class="public-id">

                                        {{ $image->public_id }}

                                    </small>

                                </td>

                            </tr>


                            {{-- Created At --}}

                            <tr>

                                <th>
                                    Uploaded At
                                </th>

                                <td>

                                    {{ $image->created_at->format('d M Y, h:i A') }}

                                </td>

                            </tr>


                        </table>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 ACTIONS
            ================================================== --}}

            <div class="card border action-card mt-3">

                <div class="card-body">

                    <h5 class="mb-3">
                        ⚡ Image Actions
                    </h5>


                    <div class="d-grid gap-2">


                        {{-- Open Original --}}

                        <a
                            href="{{ $image->secure_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-primary"
                        >
                            🔗 Open Original Image
                        </a>


                        {{-- Download --}}

                        <a
                            href="{{ route('cloudinary.download', $image) }}"
                            class="btn btn-info"
                        >
                            ⬇️ Download Original Image
                        </a>


                        {{-- Copy URL --}}

                        <button
                            type="button"
                            id="copyUrlButton"
                            class="btn btn-outline-dark"
                            onclick="copyImageUrl()"
                        >
                            📋 Copy Cloudinary URL
                        </button>


                        {{-- Thumbnail --}}

                        <a
                            href="{{ route('cloudinary.transform', [$image, 'thumbnail']) }}"
                            class="btn btn-success"
                        >
                            ✨ Thumbnail Transformation
                        </a>


                        {{-- Small --}}

                        <a
                            href="{{ route('cloudinary.transform', [$image, 'small']) }}"
                            class="btn btn-outline-success"
                        >
                            🖼️ Small Transformation
                        </a>


                        {{-- Medium --}}

                        <a
                            href="{{ route('cloudinary.transform', [$image, 'medium']) }}"
                            class="btn btn-outline-success"
                        >
                            📐 Medium Transformation
                        </a>


                        {{-- Square --}}

                        <a
                            href="{{ route('cloudinary.transform', [$image, 'square']) }}"
                            class="btn btn-outline-success"
                        >
                            ⬜ Square Transformation
                        </a>


                        {{-- Optimized --}}

                        <a
                            href="{{ route('cloudinary.transform', [$image, 'optimized']) }}"
                            class="btn btn-outline-primary"
                        >
                            ⚡ Optimized Transformation
                        </a>


                        {{-- Delete --}}

                        <button
                            type="button"
                            class="btn btn-outline-danger"
                            onclick="deleteImage()"
                        >
                            🗑️ Delete Image
                        </button>


                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         CLOUDINARY URL
    ========================================================== --}}

    <div class="card border mt-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="mb-0">
                    🔗 Cloudinary Image URL
                </h5>

                <button
                    type="button"
                    id="copyUrlButtonBottom"
                    class="btn btn-sm btn-outline-primary"
                    onclick="copyImageUrl('bottom')"
                >
                    📋 Copy URL
                </button>

            </div>


            <div class="url-box">

                {{ $image->secure_url }}

            </div>

        </div>

    </div>


    {{-- =========================================================
         TRANSFORMATION SECTION
    ========================================================== --}}

    <div class="card border mt-4">

        <div class="card-body">

            <h5 class="mb-1">
                ✨ Cloudinary Transformations
            </h5>

            <p class="text-muted">
                Generate optimized versions of this image using Cloudinary transformations.
            </p>


            <div class="row g-2">


                <div class="col-md-4">

                    <a
                        href="{{ route('cloudinary.transform', [$image, 'thumbnail']) }}"
                        class="btn btn-outline-primary w-100"
                    >
                        Thumbnail
                        <small class="d-block">
                            300 × 200
                        </small>
                    </a>

                </div>


                <div class="col-md-4">

                    <a
                        href="{{ route('cloudinary.transform', [$image, 'small']) }}"
                        class="btn btn-outline-primary w-100"
                    >
                        Small
                        <small class="d-block">
                            500 × 500
                        </small>
                    </a>

                </div>


                <div class="col-md-4">

                    <a
                        href="{{ route('cloudinary.transform', [$image, 'medium']) }}"
                        class="btn btn-outline-primary w-100"
                    >
                        Medium
                        <small class="d-block">
                            800 × 800
                        </small>
                    </a>

                </div>


                <div class="col-md-6">

                    <a
                        href="{{ route('cloudinary.transform', [$image, 'square']) }}"
                        class="btn btn-outline-success w-100"
                    >
                        ⬜ Square
                        <small class="d-block">
                            500 × 500 Crop
                        </small>
                    </a>

                </div>


                <div class="col-md-6">

                    <a
                        href="{{ route('cloudinary.transform', [$image, 'optimized']) }}"
                        class="btn btn-outline-success w-100"
                    >
                        ⚡ Optimized
                        <small class="d-block">
                            Auto Quality + Format
                        </small>
                    </a>

                </div>


            </div>

        </div>

    </div>


</div>
```

</div>

</div>

{{-- =========================================================
HIDDEN DELETE FORM
========================================================= --}}

<form
    id="deleteForm"
    method="POST"
    action="{{ route('cloudinary.destroy', $image) }}"
    style="display:none;"
>

```
@csrf

@method('DELETE')
```

</form>

{{-- =========================================================
JAVASCRIPT
========================================================= --}}

<script>

    /*
    |--------------------------------------------------------------------------
    | Copy Cloudinary URL
    |--------------------------------------------------------------------------
    */

    function copyImageUrl(position = 'top')
    {
        const url =
            @json($image->secure_url);

        const button =
            position === 'bottom'
                ? document.getElementById(
                    'copyUrlButtonBottom'
                )
                : document.getElementById(
                    'copyUrlButton'
                );


        if (!navigator.clipboard) {

            fallbackCopy(url, button);

            return;
        }


        navigator.clipboard
            .writeText(url)
            .then(function () {

                const originalText =
                    button.innerHTML;


                button.innerHTML =
                    '✅ URL Copied';


                button.classList.remove(
                    'btn-outline-dark',
                    'btn-outline-primary'
                );


                button.classList.add(
                    'copy-success'
                );


                setTimeout(function () {

                    button.innerHTML =
                        originalText;


                    button.classList.remove(
                        'copy-success'
                    );


                    if (position === 'bottom') {

                        button.classList.add(
                            'btn-outline-primary'
                        );

                    } else {

                        button.classList.add(
                            'btn-outline-dark'
                        );

                    }

                }, 1800);

            })
            .catch(function () {

                fallbackCopy(
                    url,
                    button
                );

            });
    }


    /*
    |--------------------------------------------------------------------------
    | Clipboard fallback
    |--------------------------------------------------------------------------
    */

    function fallbackCopy(url, button)
    {
        const textarea =
            document.createElement('textarea');

        textarea.value = url;

        textarea.style.position =
            'fixed';

        textarea.style.opacity =
            '0';

        document.body.appendChild(
            textarea
        );

        textarea.select();

        try {

            document.execCommand(
                'copy'
            );

            button.innerHTML =
                '✅ URL Copied';


        } catch (error) {

            alert(
                'Unable to copy the URL. Please copy it manually.'
            );

        }


        document.body.removeChild(
            textarea
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Image
    |--------------------------------------------------------------------------
    */

    function deleteImage()
    {
        const confirmed =
            confirm(
                'Are you sure you want to permanently delete this image from Cloudinary and the database?'
            );


        if (!confirmed) {

            return;

        }


        document
            .getElementById('deleteForm')
            .submit();
    }

</script>

{{-- =========================================================
BOOTSTRAP JAVASCRIPT
========================================================= --}}

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>
