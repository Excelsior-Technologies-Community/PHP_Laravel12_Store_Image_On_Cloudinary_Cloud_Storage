<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cloudinary Transformation</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f4f6f9;
        }

        .transform-card {
            border: 0;
            border-radius: 15px;
        }

        .image-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
        }

        .image-container img {
            max-width: 100%;
            max-height: 500px;
            display: block;
            margin: auto;
        }

        .code-box {
            background: #212529;
            color: #fff;
            padding: 15px;
            border-radius: 8px;
            word-break: break-all;
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

    <div class="card shadow-sm transform-card">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h3 class="mb-1">
                        ✨ Cloudinary Transformation
                    </h3>

                    <p class="text-muted mb-0">
                        Transformation:
                        <strong>
                            {{ ucfirst($transformation) }}
                        </strong>
                    </p>

                </div>

                <a
                    href="{{ route('cloudinary.show', $image) }}"
                    class="btn btn-secondary"
                >
                    ← Back
                </a>

            </div>

            <div class="row">

                {{-- Original --}}
                <div class="col-md-6 mb-4">

                    <h5>
                        Original Image
                    </h5>

                    <div class="image-container">

                        <img
                            src="{{ $image->secure_url }}"
                            alt="Original Image"
                        >

                    </div>

                </div>

                {{-- Transformed --}}
                <div class="col-md-6 mb-4">

                    <h5>
                        Transformed Image
                    </h5>

                    <div class="image-container">

                        <img
                            src="{{ $url }}"
                            alt="Transformed Image"
                        >

                    </div>

                </div>

            </div>

            <div class="mt-3">

                <h5>
                    Generated Cloudinary URL
                </h5>

                <div class="code-box">
                    {{ $url }}
                </div>

            </div>

            <div class="mt-4">

                <h5>
                    Available Transformations
                </h5>

                <div class="d-flex flex-wrap gap-2">

                    <a
                        href="{{ route('cloudinary.transform', [$image, 'thumbnail']) }}"
                        class="btn btn-outline-primary"
                    >
                        Thumbnail
                    </a>

                    <a
                        href="{{ route('cloudinary.transform', [$image, 'small']) }}"
                        class="btn btn-outline-primary"
                    >
                        Small
                    </a>

                    <a
                        href="{{ route('cloudinary.transform', [$image, 'medium']) }}"
                        class="btn btn-outline-primary"
                    >
                        Medium
                    </a>

                    <a
                        href="{{ route('cloudinary.transform', [$image, 'square']) }}"
                        class="btn btn-outline-primary"
                    >
                        Square
                    </a>

                    <a
                        href="{{ route('cloudinary.transform', [$image, 'optimized']) }}"
                        class="btn btn-outline-success"
                    >
                        Optimized
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>