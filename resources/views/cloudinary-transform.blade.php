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

                <div class="col-12 mb-4">
                    <form method="GET" class="card border p-3 row g-2">
                        <div class="col-md-2"><label class="form-label">Width</label><input type="number" name="width" min="1" max="4000" class="form-control" placeholder="px"></div>
                        <div class="col-md-2"><label class="form-label">Height</label><input type="number" name="height" min="1" max="4000" class="form-control" placeholder="px"></div>
                        <div class="col-md-2"><label class="form-label">Crop</label><select name="crop" class="form-select"><option value="">None</option><option>fill</option><option>fit</option><option>limit</option><option>thumb</option></select></div>
                        <div class="col-md-2"><label class="form-label">Rotate</label><select name="rotate" class="form-select"><option value="">None</option><option value="90">90°</option><option value="180">180°</option><option value="270">270°</option></select></div>
                        <div class="col-md-2"><label class="form-label">Quality</label><input type="number" name="quality" min="1" max="100" class="form-control" placeholder="1-100"></div>
                        <div class="col-md-2"><label class="form-label">Format</label><select name="format" class="form-select"><option value="">Auto</option><option>jpg</option><option>png</option><option>webp</option><option>avif</option></select></div>
                        <div class="col-md-12 d-flex flex-wrap gap-3"><label><input type="checkbox" name="flip_h" value="1"> Flip horizontal</label><label><input type="checkbox" name="flip_v" value="1"> Flip vertical</label><label><input type="checkbox" name="sharpen" value="1"> Sharpen</label><label><input type="checkbox" name="watermark" value="1"> Watermark</label><label><input type="checkbox" name="remove_background" value="1"> Background removal</label><label class="d-flex gap-2 align-items-center">Blur <input type="number" name="blur" min="1" max="2000" class="form-control" style="width:100px"></label></div>
                        <div class="col-12"><button class="btn btn-primary">Apply Custom Transformation</button></div>
                    </form>
                </div>

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

            <div class="mt-4">
                <h5>Responsive image URLs</h5>
                @foreach($responsiveUrls as $width => $responsiveUrl)
                    <div class="small mb-1"><strong>{{ $width }}px:</strong> <a href="{{ $responsiveUrl }}" target="_blank">{{ $responsiveUrl }}</a></div>
                @endforeach
            </div>

        </div>

    </div>

</div>

</body>
</html>