<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Local HUB</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <div class="mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="LocalHUB Logo" class="h-20 mx-auto mb-4">
                <h1 class="text-9xl font-bold text-gray-300">404</h1>
                <h2 class="text-3xl font-semibold text-gray-700 mt-4">Page Not Found</h2>
                <p class="text-gray-500 mt-2">The page you are looking for doesn't exist or has been moved.</p>
            </div>

            <div class="space-y-4">
                <a href="{{ url('/dashboard') }}"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-home mr-2"></i>
                    Return Home
                </a>

                <button onclick="window.history.back()"
                    class="block mx-auto mt-4 text-blue-600 hover:text-blue-800 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Go Back
                </button>
            </div>
        </div>
    </div>
</body>

</html>