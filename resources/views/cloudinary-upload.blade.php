<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cloudinary Image Management</title>

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

        .upload-card,
        .filter-card,
        .image-card,
        .stat-card {
            border: 0;
            border-radius: 15px;
        }

        .image-card {
            overflow: hidden;
            transition: 0.2s;
        }

        .image-card:hover {
            transform: translateY(-3px);
        }

        .image-preview {
            height: 220px;
            width: 100%;
            object-fit: cover;
            background: #f1f1f1;
        }

        .file-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .upload-row {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .upload-input-area {
            flex: 1;
        }

        .upload-button-area {
            width: 250px;
            flex-shrink: 0;
        }

        .upload-btn {
            width: 100%;
            height: 38px;
            white-space: nowrap;
        }

        .stat-number {
            font-size: 25px;
            font-weight: 700;
        }

        .filter-title {
            font-weight: 700;
        }

        .selected-card {
            outline: 3px solid #0d6efd;
        }

        .select-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .copy-btn {
            white-space: nowrap;
        }

        .skeleton {
            background: linear-gradient(90deg, #e9ecef 25%, #f8f9fa 50%, #e9ecef 75%);
            background-size: 200% 100%;
            animation: loading 1.2s infinite;
        }

        @keyframes loading { to { background-position: -200% 0; } }

        body.dark-mode { background: #151922; color: #e9ecef; }
        body.dark-mode .card, body.dark-mode .form-control, body.dark-mode .form-select { background: #202633; color: #e9ecef; border-color: #394354; }
        body.dark-mode .text-muted { color: #aeb8c7 !important; }

        @media (max-width: 767.98px) {

            .upload-row {
                display: block;
            }

            .upload-button-area {
                width: 100%;
                margin-top: 15px;
            }

            .upload-btn {
                width: 100%;
            }

        }

    </style>

</head>


<body>


{{-- =========================================================
     NAVBAR
========================================================= --}}

<nav class="navbar navbar-dark bg-dark mb-4">

    <div class="container">

        <a
            class="navbar-brand"
            href="{{ route('cloudinary.index') }}"
        >
            ☁️ Cloudinary Manager
        </a>

        <div class="d-flex gap-2">

            <a
                href="{{ route('cloudinary.analytics') }}"
                class="btn btn-outline-light"
            >
                📊 Analytics
            </a>

            <a href="{{ route('cloudinary.recycleBin') }}" class="btn btn-outline-light">♻️ Recycle Bin ({{ $trashedCount }})</a>
            <button type="button" class="btn btn-outline-light" onclick="toggleDarkMode()">🌙</button>

        </div>

    </div>

</nav>


<div class="container pb-5">


{{-- =========================================================
     SUCCESS MESSAGE
========================================================= --}}

@if(session('success'))

    <div
        class="alert alert-success alert-dismissible fade show"
        role="alert"
    >

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

@endif


{{-- =========================================================
     ERROR MESSAGE
========================================================= --}}

@if(session('error'))

    <div
        class="alert alert-danger alert-dismissible fade show"
        role="alert"
    >

        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
        ></button>

    </div>

@endif


{{-- =========================================================
     VALIDATION ERRORS
========================================================= --}}

@if($errors->any())

    <div class="alert alert-danger">

        <strong>
            Please fix the following:
        </strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

    </div>

@endif

@if($failedUploads->isNotEmpty())
    <div class="alert alert-warning">
        <strong>Failed uploads</strong>
        @foreach($failedUploads as $failed)
            <div class="d-flex justify-content-between align-items-center mt-2">
                <span>{{ $failed->original_name }} <small>({{ $failed->failure_message }})</small></span>
                <form method="POST" action="{{ route('cloudinary.retry', $failed->id) }}">@csrf<button class="btn btn-sm btn-warning">Retry</button></form>
            </div>
        @endforeach
    </div>
@endif


{{-- =========================================================
     STATISTICS
========================================================= --}}

<div class="row mb-4">

    <div class="col-lg-4 col-md-6 mb-3">

        <div class="card shadow-sm stat-card">

            <div class="card-body">

                <div class="text-muted">
                    Total Images
                </div>

                <div class="stat-number text-primary">
                    {{ $totalImages }}
                </div>

            </div>

        </div>

    </div>


    <div class="col-lg-4 col-md-6 mb-3">

        <div class="card shadow-sm stat-card">

            <div class="card-body">

                <div class="text-muted">
                    Filtered Images
                </div>

                <div class="stat-number text-success">
                    {{ $filteredImages }}
                </div>

            </div>

        </div>

    </div>


    <div class="col-lg-4 col-md-12 mb-3">

        <div class="card shadow-sm stat-card">

            <div class="card-body">

                <div class="text-muted">
                    Total Cloud Storage
                </div>

                <div class="stat-number text-warning">

                    {{ number_format(($totalStorage ?? 0) / 1024 / 1024, 2) }}

                    MB

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     UPLOAD SECTION
========================================================= --}}

<div class="card shadow-sm upload-card mb-4">

    <div class="card-body p-4">

        <h3 class="mb-1">
            📤 Upload Image to Cloudinary
        </h3>

        <p class="text-muted mb-4">
            Images are stored directly in Cloudinary Cloud Storage.
        </p>


        <form
            method="POST"
            action="{{ route('cloudinary.upload') }}"
            enctype="multipart/form-data"
        >

            @csrf

            <div class="upload-row">


                <div class="upload-input-area">

                    <label
                        for="image"
                        class="form-label fw-semibold"
                    >
                        Select Image
                    </label>

                    <input
                        type="file"
                        name="image"
                        id="image"
                        class="form-control"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        required
                    >

                    <small class="text-muted">
                        JPG, JPEG, PNG, GIF or WEBP — Maximum 5 MB
                    </small>

                    <div class="row g-2 mt-2">
                        <div class="col-md-6"><input type="text" name="title" class="form-control" placeholder="Image title"></div>
                        <div class="col-md-6"><input type="text" name="category" class="form-control" placeholder="Folder / category"></div>
                        <div class="col-md-6"><input type="text" name="tags" class="form-control" placeholder="Tags: product, banner"></div>
                        <div class="col-md-6"><textarea name="description" class="form-control" rows="1" placeholder="Image description"></textarea></div>
                    </div>

                </div>


                <div class="upload-button-area">

                    <button
                        type="submit"
                        class="btn btn-primary upload-btn"
                    >
                        ☁️ Upload to Cloudinary
                    </button>

                </div>


            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     FILTER SECTION
========================================================= --}}

<div class="card shadow-sm filter-card mb-4">

    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h4 class="mb-1">
                    🔎 Image Filters
                </h4>

                <small class="text-muted">
                    Search, filter and sort your Cloudinary images.
                </small>

            </div>

            <a
                href="{{ route('cloudinary.index') }}"
                class="btn btn-outline-secondary btn-sm"
            >
                ✕ Clear Filters
            </a>

        </div>


        <form
            method="GET"
            action="{{ route('cloudinary.index') }}"
        >

            <div class="row g-3">


                {{-- Search --}}

                <div class="col-lg-4 col-md-6">

                    <label class="form-label filter-title">
                        🔎 Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Filename or Public ID..."
                    >

                </div>


                {{-- Format --}}

                <div class="col-lg-2 col-md-6">

                    <label class="form-label filter-title">
                        🖼️ Format
                    </label>

                    <select
                        name="format"
                        class="form-select"
                    >

                        <option value="">
                            All Formats
                        </option>

                        @foreach($formats as $format)

                            <option
                                value="{{ $format }}"
                                {{ request('format') === $format ? 'selected' : '' }}
                            >
                                {{ strtoupper($format) }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label filter-title">📁 Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label filter-title">⭐ Favorites</label>
                    <select name="favorites" class="form-select">
                        <option value="">All Images</option>
                        <option value="1" {{ request('favorites') ? 'selected' : '' }}>Favorites Only</option>
                    </select>
                </div>


                {{-- Date --}}

                <div class="col-lg-2 col-md-6">

                    <label class="form-label filter-title">
                        📅 Date
                    </label>

                    <select
                        name="date_filter"
                        class="form-select"
                    >

                        <option value="">
                            All Dates
                        </option>

                        <option
                            value="today"
                            {{ request('date_filter') === 'today' ? 'selected' : '' }}
                        >
                            Today
                        </option>

                        <option
                            value="week"
                            {{ request('date_filter') === 'week' ? 'selected' : '' }}
                        >
                            This Week
                        </option>

                        <option
                            value="month"
                            {{ request('date_filter') === 'month' ? 'selected' : '' }}
                        >
                            This Month
                        </option>

                    </select>

                </div>


                {{-- Sorting --}}

                <div class="col-lg-4 col-md-6">

                    <label class="form-label filter-title">
                        ↕️ Sort By
                    </label>

                    <select
                        name="sort"
                        class="form-select"
                    >

                        <option
                            value="newest"
                            {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}
                        >
                            Newest First
                        </option>

                        <option
                            value="oldest"
                            {{ request('sort') === 'oldest' ? 'selected' : '' }}
                        >
                            Oldest First
                        </option>

                        <option
                            value="largest"
                            {{ request('sort') === 'largest' ? 'selected' : '' }}
                        >
                            Largest File
                        </option>

                        <option
                            value="smallest"
                            {{ request('sort') === 'smallest' ? 'selected' : '' }}
                        >
                            Smallest File
                        </option>

                        <option
                            value="name_asc"
                            {{ request('sort') === 'name_asc' ? 'selected' : '' }}
                        >
                            Name A-Z
                        </option>

                        <option
                            value="name_desc"
                            {{ request('sort') === 'name_desc' ? 'selected' : '' }}
                        >
                            Name Z-A
                        </option>

                    </select>

                </div>


                {{-- Minimum Size --}}

                <div class="col-lg-3 col-md-6">

                    <label class="form-label filter-title">
                        📦 Minimum Size (KB)
                    </label>

                    <input
                        type="number"
                        name="min_size"
                        value="{{ request('min_size') }}"
                        min="0"
                        step="0.01"
                        class="form-control"
                        placeholder="Example: 100"
                    >

                </div>


                {{-- Maximum Size --}}

                <div class="col-lg-3 col-md-6">

                    <label class="form-label filter-title">
                        📦 Maximum Size (KB)
                    </label>

                    <input
                        type="number"
                        name="max_size"
                        value="{{ request('max_size') }}"
                        min="0"
                        step="0.01"
                        class="form-control"
                        placeholder="Example: 5000"
                    >

                </div>


                {{-- Submit --}}

                <div class="col-lg-6 col-md-12 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >
                        🔎 Apply Filters
                    </button>

                </div>


            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     BULK ACTION BAR
========================================================= --}}

<form
    method="POST"
    action="{{ route('cloudinary.bulkDestroy') }}"
    id="bulkDeleteForm"
>

    @csrf

    @method('DELETE')


    <div
        class="card shadow-sm mb-4"
        id="bulkActionBar"
        style="display:none;"
    >

        <div class="card-body d-flex justify-content-between align-items-center">

            <div>

                <strong>
                    <span id="selectedCount">0</span>
                    image(s) selected
                </strong>

            </div>

            <div class="d-flex gap-2">

                <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm"
                    onclick="clearSelection()"
                >
                    Clear Selection
                </button>

                <button
                    type="submit"
                    class="btn btn-danger btn-sm"
                    onclick="return confirmBulkDelete()"
                >
                    🗑️ Delete Selected
                </button>

                <button type="submit" formaction="{{ route('cloudinary.zip') }}" formmethod="POST" class="btn btn-outline-primary btn-sm">
                    ZIP Export
                </button>

            </div>

        </div>

    </div>


    {{-- =========================================================
         GALLERY HEADER
    ========================================================= --}}

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h3 class="mb-0">
                🖼️ Cloudinary Image Gallery
            </h3>

            <small class="text-muted">

                Showing
                <strong>{{ $images->count() }}</strong>
                of
                <strong>{{ $images->total() }}</strong>
                filtered image(s)

            </small>

        </div>


        <div>

            <button
                type="button"
                class="btn btn-outline-primary btn-sm"
                onclick="selectAllVisible()"
            >
                ☑️ Select Page
            </button>

        </div>

    </div>


    {{-- =========================================================
         GALLERY
    ========================================================= --}}

    <div class="row">


        @forelse($images as $image)


            <div
                class="col-lg-3 col-md-4 col-sm-6 mb-4"
            >


                <div
                    class="card shadow-sm image-card h-100"
                    id="card-{{ $image->id }}"
                >


                    {{-- Image --}}

                    <div class="position-relative">

                        <img
                            src="{{ $image->secure_url }}"
                            alt="{{ $image->original_name }}"
                            class="image-preview"
                        >


                        {{-- Select Checkbox --}}

                        <div
                            class="position-absolute top-0 start-0 p-2"
                        >

                            <input
                                type="checkbox"
                                name="images[]"
                                value="{{ $image->id }}"
                                class="form-check-input select-checkbox image-checkbox"
                                onchange="updateSelection()"
                                title="Select image"
                            >

                        </div>


                        {{-- Format Badge --}}

                        <span
                            class="position-absolute top-0 end-0 m-2 badge bg-dark"
                        >
                            {{ strtoupper($image->format) }}
                        </span>

                    </div>


                    <div class="card-body">


                        {{-- File Name --}}

                        <h6
                            class="file-name"
                            title="{{ $image->original_name }}"
                        >
                            {{ $image->display_title }}
                        </h6>

                        @if($image->is_favorite)<span class="badge bg-warning text-dark">⭐ Favorite</span>@endif
                        @if($image->tags)<div class="small text-primary mb-2">#{{ implode(' #', $image->tags) }}</div>@endif


                        {{-- Information --}}

                        <div class="small text-muted mb-3">

                            <div>

                                📦 Size:

                                <strong>
                                    {{ $image->formatted_size }}
                                </strong>

                            </div>


                            <div>

                                📐 Dimensions:

                                <strong>
                                    {{ $image->dimensions }}
                                </strong>

                            </div>


                            <div>

                                📅 Uploaded:

                                <strong>
                                    {{ $image->created_at->format('d M Y') }}
                                </strong>

                            </div>

                        </div>


                        {{-- Actions --}}

                        <div class="d-grid gap-2">


                            {{-- View Details --}}

                            <a
                                href="{{ route('cloudinary.show', $image) }}"
                                class="btn btn-sm btn-outline-primary"
                            >
                                👁️ View Details
                            </a>


                            {{-- Transform --}}

                            <a
                                href="{{ route('cloudinary.transform', [$image, 'thumbnail']) }}"
                                class="btn btn-sm btn-outline-success"
                            >
                                ✨ Transform
                            </a>


                            {{-- Copy URL --}}

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-dark copy-btn"
                                onclick="copyImageUrl(
                                    '{{ $image->secure_url }}',
                                    this
                                )"
                            >
                                📋 Copy URL
                            </button>


                            {{-- Download --}}

                            <a
                                href="{{ route('cloudinary.download', $image) }}"
                                class="btn btn-sm btn-outline-info"
                            >
                                ⬇️ Download
                            </a>


                            {{-- Delete --}}

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-danger"
                                onclick="deleteSingleImage(
                                    '{{ route('cloudinary.destroy', $image) }}'
                                )"
                            >
                                🗑️ Delete
                            </button>

                        </div>


                    </div>


                </div>


            </div>


        @empty


            <div class="col-12">

                <div class="alert alert-info text-center">

                    <h5>
                        🔍 No images found
                    </h5>

                    <p class="mb-0">
                        Try changing your search or filters.
                    </p>

                </div>

            </div>

        @endforelse


    </div>


</form>


{{-- =========================================================
     PAGINATION
========================================================= --}}

@if($images->hasPages())

    <div class="d-flex justify-content-center mt-3">

        {{ $images->links('pagination::bootstrap-5') }}

    </div>

@endif


{{-- =========================================================
     JAVASCRIPT
========================================================= --}}

<script>

    /*
    |--------------------------------------------------------------------------
    | Update selected images
    |--------------------------------------------------------------------------
    */

    function updateSelection()
    {
        const checkboxes =
            document.querySelectorAll('.image-checkbox');

        let selected = 0;

        checkboxes.forEach(function (checkbox) {

            const card =
                document.getElementById(
                    'card-' + checkbox.value
                );

            if (checkbox.checked) {

                selected++;

                if (card) {
                    card.classList.add(
                        'selected-card'
                    );
                }

            } else {

                if (card) {
                    card.classList.remove(
                        'selected-card'
                    );
                }
            }

        });


        document.getElementById(
            'selectedCount'
        ).innerText = selected;


        document.getElementById(
            'bulkActionBar'
        ).style.display =
            selected > 0 ? 'block' : 'none';
    }


    /*
    |--------------------------------------------------------------------------
    | Select all images on current page
    |--------------------------------------------------------------------------
    */

    function selectAllVisible()
    {
        const checkboxes =
            document.querySelectorAll('.image-checkbox');

        checkboxes.forEach(function (checkbox) {

            checkbox.checked = true;

        });

        updateSelection();
    }


    /*
    |--------------------------------------------------------------------------
    | Clear selection
    |--------------------------------------------------------------------------
    */

    function clearSelection()
    {
        const checkboxes =
            document.querySelectorAll('.image-checkbox');

        checkboxes.forEach(function (checkbox) {

            checkbox.checked = false;

        });

        updateSelection();
    }


    /*
    |--------------------------------------------------------------------------
    | Confirm bulk deletion
    |--------------------------------------------------------------------------
    */

    function confirmBulkDelete()
    {
        const selected =
            document.querySelectorAll(
                '.image-checkbox:checked'
            ).length;

        if (selected === 0) {

            alert(
                'Please select at least one image.'
            );

            return false;
        }

        return confirm(
            'Are you sure you want to permanently delete '
            + selected
            + ' selected image(s) from Cloudinary?'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Copy Cloudinary URL
    |--------------------------------------------------------------------------
    */

    function copyImageUrl(url, button)
    {
        navigator.clipboard.writeText(url)
            .then(function () {

                const originalText =
                    button.innerHTML;

                button.innerHTML =
                    '✅ URL Copied';

                button.classList.remove(
                    'btn-outline-dark'
                );

                button.classList.add(
                    'btn-success'
                );

                setTimeout(function () {

                    button.innerHTML =
                        originalText;

                    button.classList.remove(
                        'btn-success'
                    );

                    button.classList.add(
                        'btn-outline-dark'
                    );

                }, 1800);

            })
            .catch(function () {

                alert(
                    'Unable to copy URL. Please copy it manually.'
                );

            });
    }


    /*
    |--------------------------------------------------------------------------
    | Single delete confirmation
    |--------------------------------------------------------------------------
    */

    function deleteSingleImage(action)
    {
        const form = document.createElement('form');

        form.method = 'POST';
        form.action = action;


        const csrf =
            document.createElement('input');

        csrf.type = 'hidden';

        csrf.name = '_token';

        csrf.value =
            '{{ csrf_token() }}';


        const method =
            document.createElement('input');

        method.type = 'hidden';

        method.name = '_method';

        method.value = 'DELETE';


        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);

        document.getElementById('confirmDeleteButton').onclick = function () {
            form.submit();
        };
        new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
    }

    function toggleDarkMode()
    {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('cloudinary-dark-mode', document.body.classList.contains('dark-mode') ? '1' : '0');
    }

    if (localStorage.getItem('cloudinary-dark-mode') === '1') document.body.classList.add('dark-mode');

</script>

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Confirm deletion</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">Move this image to the recycle bin?</div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger" id="confirmDeleteButton">Move to Recycle Bin</button></div>
    </div></div>
</div>


{{-- =========================================================
     BOOTSTRAP JS
========================================================= --}}

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>