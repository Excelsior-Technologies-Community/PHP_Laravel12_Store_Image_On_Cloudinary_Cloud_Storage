<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recycle Bin - Cloudinary Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark mb-4"><div class="container"><a class="navbar-brand" href="{{ route('cloudinary.index') }}">Cloudinary Manager</a><a class="btn btn-outline-light" href="{{ route('cloudinary.index') }}">Gallery</a></div></nav>
<main class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h2>Recycle Bin</h2><p class="text-muted mb-0">Restore images or permanently delete them.</p></div><span class="badge bg-secondary">{{ $images->total() }} image(s)</span></div>
    <div class="row">
        @forelse($images as $image)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4"><div class="card h-100 shadow-sm">
                <img src="{{ $image->secure_url }}" class="card-img-top" style="height:190px;object-fit:cover" alt="{{ $image->original_name }}">
                <div class="card-body"><h6 class="text-truncate">{{ $image->display_title }}</h6><small class="text-muted d-block mb-3">Deleted {{ $image->deleted_at?->format('d M Y, h:i A') }}</small>
                    <form method="POST" action="{{ route('cloudinary.restore', $image) }}" class="d-grid mb-2">@csrf @method('PATCH')<button class="btn btn-success">Restore</button></form>
                    <form method="POST" action="{{ route('cloudinary.permanentDestroy', $image) }}" class="d-grid" onsubmit="return confirm('Permanently delete this image?')">@csrf @method('DELETE')<button class="btn btn-outline-danger">Delete Permanently</button></form>
                </div>
            </div></div>
        @empty
            <div class="col-12"><div class="alert alert-info">Recycle bin is empty.</div></div>
        @endforelse
    </div>
    {{ $images->links() }}
</main>
</body>
</html>
