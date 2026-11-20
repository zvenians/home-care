<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="theme-color" content="#0b635b">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Home Care UNAI | Perawatan lebih dekat</title>
  <link rel="icon" type="image/png" href="/assets/logo-unai.png">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
  <div id="app" aria-live="polite"></div>
  <script>window.HOMECARE_CONFIG = {api: true, demo: true, assets: '/assets/'};</script>
</body>
</html>
