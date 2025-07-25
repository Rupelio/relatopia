<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Relatopia</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3613355728057212"
     crossorigin="anonymous"></script>
    </head>
    <body class="bg-gray-50 min-h-screen">
        <nav class="fixed top-0 left-0 right-0 bg-white/95 backdrop-blur-md border-b border-emerald-100 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="/" class="flex items-center hover:opacity-80 transition-opacity duration-200">
                            <img class="h-8 w-auto" src="/relatopia.png" alt="Relatopia">
                        </a>
                    </div>

                    <!-- Links de navegação -->
                    <div class="flex items-center space-x-6">
                        <a href="/login" class="text-gray-700 hover:text-emerald-600 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 hover:bg-emerald-50">
                            Login
                        </a>
                        <a href="/cadastro" class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:shadow-lg hover:from-emerald-700 hover:to-teal-700 transform hover:-translate-y-0.5 transition-all duration-200">
                            Cadastro
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="container mx-auto px-4 pt-16">
            {{ $slot }}
        </div>
        <footer class="bg-gradient-to-r from-emerald-50 to-teal-50 border-t border-emerald-100 py-3">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <!-- Texto principal -->
                    <div class="text-center sm:text-left">
                        <p class="text-xs sm:text-sm text-emerald-700 font-medium">
                            Created by
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600 font-semibold">
                                Rupelio
                            </span>
                        </p>
                    </div>

                    <!-- Decoração opcional -->
                    <div class="flex items-center space-x-1">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                        <div class="w-1.5 h-1.5 bg-teal-400 rounded-full animate-pulse delay-100"></div>
                        <div class="w-1 h-1 bg-emerald-300 rounded-full animate-pulse delay-200"></div>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
