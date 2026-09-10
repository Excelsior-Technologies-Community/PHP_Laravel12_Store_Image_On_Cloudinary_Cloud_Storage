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

        .upload-card {
            border: 0;
            border-radius: 15px;
        }

        .image-card {
            border: 0;
            border-radius: 15px;
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

        /* =========================
           Upload Area
        ========================== */

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

        .file-help-text {
            display: block;
            margin-top: 5px;
        }


        /* =========================
           Responsive
        ========================== */

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


    {{-- =========================
         Navigation Bar
    ========================== --}}

    <nav class="navbar navbar-dark bg-dark mb-4">

        <div class="container">

            <a
                class="navbar-brand"
                href="{{ route('cloudinary.index') }}"
            >
                ☁️ Cloudinary Manager
            </a>

            <a
                href="{{ route('cloudinary.analytics') }}"
                class="btn btn-outline-light"
            >
                📊 Analytics
            </a>

        </div>

    </nav>


    <div class="container pb-5">


        {{-- =========================
             Success Message
        ========================== --}}

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


        {{-- =========================
             Error Message
        ========================== --}}

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


        {{-- =========================
             Validation Errors
        ========================== --}}

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


        {{-- =========================
             Upload Section
        ========================== --}}

        <div class="card shadow-sm upload-card mb-4">

            <div class="card-body p-4">

                <h3 class="mb-1">
                    Upload Image to Cloudinary
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


                        {{-- =========================
                             File Input
                        ========================== --}}

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

                            <small class="text-muted file-help-text">
                                JPG, JPEG, PNG, GIF or WEBP — Maximum 5 MB
                            </small>

                        </div>


                        {{-- =========================
                             Upload Button
                        ========================== --}}

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


        {{-- =========================
             Gallery Header
        ========================== --}}

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h3 class="mb-0">
                    🖼️ Cloudinary Image Gallery
                </h3>

                <small class="text-muted">
                    {{ $images->total() }} image(s) stored
                </small>

            </div>

        </div>


        {{-- =========================
             Gallery
        ========================== --}}

        <div class="row">


            @forelse($images as $image)


                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">


                    <div class="card shadow-sm image-card h-100">


                        {{-- Image --}}

                        <img
                            src="{{ $image->secure_url }}"
                            alt="{{ $image->original_name }}"
                            class="image-preview"
                        >


                        <div class="card-body">


                            {{-- File Name --}}

                            <h6
                                class="file-name"
                                title="{{ $image->original_name }}"
                            >
                                {{ $image->original_name }}
                            </h6>


                            {{-- Image Information --}}

                            <div class="small text-muted mb-2">

                                <div>

                                    Format:

                                    <strong>
                                        {{ strtoupper($image->format) }}
                                    </strong>

                                </div>


                                <div>

                                    Size:

                                    <strong>
                                        {{ $image->formatted_size }}
                                    </strong>

                                </div>


                                <div>

                                    Dimensions:

                                    <strong>
                                        {{ $image->dimensions }}
                                    </strong>

                                </div>

                            </div>


                            {{-- Action Buttons --}}

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


                                {{-- Delete --}}

                                <form
                                    method="POST"
                                    action="{{ route('cloudinary.destroy', $image) }}"
                                    onsubmit="return confirm('Are you sure you want to permanently delete this image from Cloudinary?');"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger w-100"
                                    >
                                        🗑️ Delete
                                    </button>

                                </form>


                            </div>


                        </div>


                    </div>


                </div>


            @empty


                <div class="col-12">

                    <div class="alert alert-info text-center">

                        No images have been uploaded yet.

                    </div>

                </div>


            @endforelse


        </div>


        {{-- =========================
             Pagination
        ========================== --}}

        @if($images->hasPages())

            <div class="d-flex justify-content-center mt-3">

                {{ $images->links('pagination::bootstrap-5') }}

            </div>

        @endif


    </div>


    {{-- =========================
         Bootstrap JavaScript
    ========================== --}}

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>


</body>

</html>