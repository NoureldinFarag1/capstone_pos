<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local HUB - Connect Your Community</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-2xl overflow-hidden">
            <div class="p-8 md:p-16 text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-blue-800 mb-6">Welcome to Local HUB</h1>
                <p class="text-xl text-gray-600 mb-10">
                    Connect, Collaborate, and Grow with Your Local Community
                </p>

                <div class="flex justify-center space-x-4">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(url('/dashboard')); ?>" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md">
                            Go to Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md">
                            Log In
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="bg-blue-50 p-4 text-center text-gray-500">
                © <?php echo e(date('Y')); ?> Local HUB. Connecting Communities.
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /Users/noureldinfarag/capstone_pos/resources/views/welcome.blade.php ENDPATH**/ ?>