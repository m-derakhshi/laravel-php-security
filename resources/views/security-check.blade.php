<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Security Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
    <h2 class="text-center mb-4">PHP Security Check</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
        {!! $output !!}
    </div>

    @if (!empty($settingsToFix))
        <h2 class="mt-4">Recommended PHP.ini Settings Adjustments:</h2>
        <p>To enhance security, update your php.ini file with the following adjustments:</p>
        <pre class="bg-light p-3 border">{{ implode("\n", $settingsToFix) }}</pre>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
