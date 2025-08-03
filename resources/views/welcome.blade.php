<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Local HUB</title>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-touch-fullscreen" content="yes">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/ipad.css') }}">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
        }

        .card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 1rem;
            opacity: 0;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card:hover::after {
            opacity: 1;
        }

        .btn {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #1e3a8a 100%);
            color: white;
            padding: 0.75rem 2.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            letter-spacing: 0.05em;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            position: relative;
            z-index: 1;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e3a8a 0%, #dc2626 100%);
            border-radius: 0.5rem;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3);
        }

        .btn:hover::after {
            opacity: 1;
        }

        .btn:active {
            transform: translateY(1px) scale(0.98);
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.4);
            transition: all 0.1s ease;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 2.75rem;
        }

        p {
            letter-spacing: 0.03em;
            line-height: 1.6;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-center items-center p-6">
    <div class="card max-w-lg w-full mx-auto p-8">
        <h1 class="text-3xl md:text-4xl font-semibold mb-4 text-center text-gray-800">
            Welcome to Local HUB
        </h1>
        <p class="text-lg mb-8 text-center text-gray-600">
            Connect, Collaborate, and Grow with Your Local Community.
        </p>
        <div class="flex justify-center">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn">
                    Control the HUB
                </a>
            @else
                <a href="{{ route('login') }}" class="btn">
                    Login the HUB
                </a>
            @endauth
        </div>
    </div>
    <footer class="mt-8 text-center text-gray-500">
        © {{ date('Y') }} Local HUB
    </footer>
</body>
</html>
