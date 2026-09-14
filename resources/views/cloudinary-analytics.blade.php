<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cloudinary Analytics</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f4f6f9;
        }

        .stat-card {
            border: 0;
            border-radius: 15px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
        }

        .analytics-card {
            border: 0;
            border-radius: 15px;
        }

        .recent-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }

        body.dark-mode { background: #151922; color: #e9ecef; }
        body.dark-mode .card, body.dark-mode .table { background: #202633; color: #e9ecef; }
        body.dark-mode .text-muted { color: #aeb8c7 !important; }

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

        <a
            href="{{ route('cloudinary.index') }}"
            class="btn btn-outline-light"
        >
            🖼️ Gallery
        </a>

        <button type="button" class="btn btn-outline-light" onclick="toggleDarkMode()">🌙</button>

    </div>

</nav>

<div class="container pb-5">

    <div class="mb-4">

        <h2>
            📊 Cloudinary Analytics Dashboard
        </h2>

        <p class="text-muted">
            Monitor uploaded images and Cloudinary storage metadata.
        </p>

    </div>

    {{-- Statistics --}}
    <div class="row">

        <div class="col-lg-3 col-md-6 mb-4">

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

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card shadow-sm stat-card">

                <div class="card-body">

                    <div class="text-muted">
                        Total Storage
                    </div>

                    <div class="stat-number text-success">
                        {{ number_format($totalBytes / 1024 / 1024, 2) }}
                        MB
                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card shadow-sm stat-card">

                <div class="card-body">

                    <div class="text-muted">
                        Uploaded Today
                    </div>

                    <div class="stat-number text-warning">
                        {{ $todayUploads }}
                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card shadow-sm stat-card">

                <div class="card-body">

                    <div class="text-muted">
                        This Week
                    </div>

                    <div class="stat-number text-danger">
                        {{ $weekUploads }}
                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- Second row --}}
    <div class="row">

        <div class="col-md-6 mb-4">

            <div class="card shadow-sm analytics-card">

                <div class="card-body">

                    <h5>
                        📈 Storage Statistics
                    </h5>

                    <hr>

                    <div class="d-flex justify-content-between mb-3">

                        <span>
                            Average Image Size
                        </span>

                        <strong>
                            {{ number_format(($averageSize ?? 0) / 1024, 2) }}
                            KB
                        </strong>

                    </div>

                    <div class="d-flex justify-content-between">

                        <span>
                            Total Storage
                        </span>

                        <strong>
                            {{ number_format($totalBytes / 1024 / 1024, 2) }}
                            MB
                        </strong>

                    </div>

                    @if($largestImage)

                        <hr>

                        <h6>
                            Largest Image
                        </h6>

                        <div class="d-flex align-items-center mt-3">

                            <img
                                src="{{ $largestImage->secure_url }}"
                                class="recent-image me-3"
                                alt="Largest image"
                            >

                            <div>

                                <strong>
                                    {{ $largestImage->original_name }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    {{ $largestImage->formatted_size }}
                                </small>

                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>

        <div class="col-md-6 mb-4">

            <div class="card shadow-sm analytics-card">

                <div class="card-body">

                    <h5>
                        📁 Image Format Statistics
                    </h5>

                    <hr>

                    @forelse($formatStatistics as $format)

                        <div class="d-flex justify-content-between mb-3">

                            <span>
                                {{ strtoupper($format->format) }}
                            </span>

                            <span class="badge bg-primary">
                                {{ $format->total }}
                            </span>

                        </div>

                    @empty

                        <p class="text-muted">
                            No format statistics available.
                        </p>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

    {{-- Recent uploads --}}
    <div class="card shadow-sm analytics-card">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    🕒 Recent Uploads
                </h5>

                <a
                    href="{{ route('cloudinary.index') }}"
                    class="btn btn-sm btn-primary"
                >
                    View Gallery
                </a>

            </div>

            <hr>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr>

                            <th>Image</th>

                            <th>Name</th>

                            <th>Format</th>

                            <th>Size</th>

                            <th>Dimensions</th>

                            <th>Uploaded</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($recentImages as $image)

                            <tr>

                                <td>

                                    <img
                                        src="{{ $image->secure_url }}"
                                        class="recent-image"
                                        alt="{{ $image->original_name }}"
                                    >

                                </td>

                                <td>
                                    {{ $image->original_name }}
                                </td>

                                <td>
                                    {{ strtoupper($image->format) }}
                                </td>

                                <td>
                                    {{ $image->formatted_size }}
                                </td>

                                <td>
                                    {{ $image->dimensions }}
                                </td>

                                <td>
                                    {{ $image->created_at->format('d M Y, h:i A') }}
                                </td>

                                <td>

                                    <a
                                        href="{{ route('cloudinary.show', $image) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        View
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center text-muted"
                                >
                                    No images uploaded yet.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3"><div id="dashboardToast" class="toast" role="alert"><div class="toast-body">Dashboard loaded</div></div></div>

<script>
    function toggleDarkMode() { document.body.classList.toggle('dark-mode'); localStorage.setItem('cloudinary-dark-mode', document.body.classList.contains('dark-mode') ? '1' : '0'); }
    if (localStorage.getItem('cloudinary-dark-mode') === '1') document.body.classList.add('dark-mode');
    window.addEventListener('load', function () { if (window.bootstrap) new bootstrap.Toast(document.getElementById('dashboardToast'), { delay: 1600 }).show(); });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>