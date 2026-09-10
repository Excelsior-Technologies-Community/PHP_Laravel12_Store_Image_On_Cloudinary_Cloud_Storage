<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Image Details - Cloudinary</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f4f6f9;
        }

        .preview {
            width: 100%;
            max-height: 550px;
            object-fit: contain;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .detail-card {
            border: 0;
            border-radius: 15px;
        }
    </style>

</head>

<body>

<nav class="navbar navbar-dark bg-dark mb-4">

    <div class="container">

        <a
            href="{{ route('cloudinary.index') }}"
            class="navbar-brand"
        >
            ☁️ Cloudinary Manager
        </a>

    </div>

</nav>

<div class="container pb-5">

    <div class="card shadow-sm detail-card">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h3 class="mb-0">
                    🖼️ Image Details
                </h3>

                <a
                    href="{{ route('cloudinary.index') }}"
                    class="btn btn-secondary"
                >
                    ← Back
                </a>

            </div>

            <div class="row">

                <div class="col-lg-7 mb-4">

                    <img
                        src="{{ $image->secure_url }}"
                        alt="{{ $image->original_name }}"
                        class="preview"
                    >

                </div>

                <div class="col-lg-5">

                    <table class="table table-bordered">

                        <tr>
                            <th>Original Name</th>
                            <td>{{ $image->original_name }}</td>
                        </tr>

                        <tr>
                            <th>Format</th>
                            <td>{{ strtoupper($image->format) }}</td>
                        </tr>

                        <tr>
                            <th>File Size</th>
                            <td>{{ $image->formatted_size }}</td>
                        </tr>

                        <tr>
                            <th>Dimensions</th>
                            <td>{{ $image->dimensions }}</td>
                        </tr>

                        <tr>
                            <th>Resource Type</th>
                            <td>{{ $image->resource_type }}</td>
                        </tr>

                        <tr>
                            <th>Folder</th>
                            <td>{{ $image->folder }}</td>
                        </tr>

                        <tr>
                            <th>Public ID</th>
                            <td>
                                <small>
                                    {{ $image->public_id }}
                                </small>
                            </td>
                        </tr>

                        <tr>
                            <th>Uploaded At</th>
                            <td>
                                {{ $image->created_at->format('d M Y, h:i A') }}
                            </td>
                        </tr>

                    </table>

                    <div class="d-grid gap-2">

                        <a
                            href="{{ $image->secure_url }}"
                            target="_blank"
                            class="btn btn-primary"
                        >
                            🔗 Open Original Image
                        </a>

                        <a
                            href="{{ route('cloudinary.transform', [$image, 'thumbnail']) }}"
                            class="btn btn-success"
                        >
                            ✨ Thumbnail Transformation
                        </a>

                        <a
                            href="{{ route('cloudinary.transform', [$image, 'medium']) }}"
                            class="btn btn-outline-success"
                        >
                            📐 Medium Transformation
                        </a>

                        <a
                            href="{{ route('cloudinary.transform', [$image, 'square']) }}"
                            class="btn btn-outline-success"
                        >
                            ⬜ Square Transformation
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>