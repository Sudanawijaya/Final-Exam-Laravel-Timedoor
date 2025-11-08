<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Perpustakaan Buku') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>


    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col antialiased">

    {{-- Minimal Navbar --}}
    <header class="bg-white/80 backdrop-blur-md border-b border-gray-200/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-light tracking-tight text-gray-900">
                    Perpustakaan
                </h1>
                <nav class="flex gap-8">
                    <a href="{{ route('books.index') }}" 
                       class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        Buku
                    </a>
                    <a href="{{ route('authors.top') }}" 
                       class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        Penulis
                    </a>
                    <a href="{{ route('ratings.create') }}" 
                       class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        Beri Rating
                    </a>
                </nav>
            </div>
        </div>
    </header>

    {{-- Main content --}}
    <main class="flex-1 max-w-7xl mx-auto w-full px-6 py-12">
        @yield('content')
    </main>

    {{-- Minimal Footer --}}
    <footer class="border-t border-gray-200/50 py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm text-gray-500 font-light">
                {{ date('Y') }} — Perpustakaan Buku
            </p>
        </div>
    </footer>

</body>
</html>