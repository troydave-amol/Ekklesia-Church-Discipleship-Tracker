<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Church Discipleship Tracker</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --blue-50: #eff6ff;
            --blue-600: #2563eb;
            --blue-700: #1d4ed8;
            --gray-600: #4b5563;
            --gray-900: #111827;
            --red-50: #fef2f2;
            --red-500: #ef4444;
            --red-700: #b91c1c;
        }
    </style>
</head>
<body class="bg-gray-50">
    @yield('content')
</body>
</html>
