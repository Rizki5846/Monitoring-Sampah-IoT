<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Monitoring') }}</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            transition: all 0.3s;
        }
        .sidebar .nav-link {
            color: #adb5bd;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: #495057;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                z-index: 1030;
                left: -250px;
                width: 250px;
            }
            .sidebar.show {
                left: 0;
            }
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 100%;
                background-color: rgba(0,0,0,0.5);
                z-index: 1029;
                display: none;
            }
            .overlay.show {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div id="overlay" class="overlay" onclick="toggleSidebar()"></div>
    
    <div class="d-flex">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar d-flex flex-column p-3 text-white">
            <a href="/" class="d-flex align-items-center mb-3 text-white text-decoration-none fs-5 fw-bold">
                <i class="bi bi-recycle me-2"></i> SmartBin
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('devices.index') }}" class="nav-link {{ request()->is('devices*') ? 'active' : '' }}">
                        <i class="bi bi-cpu me-2"></i> Devices
                    </a>
                </li>
            </ul>
            <hr>
            <div>
                <a href="{{ route('logout') }}" class="nav-link text-danger"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </nav>

        <!-- Content -->
        <div class="flex-grow-1 w-100">
            <!-- Top Navbar -->
            <nav class="navbar navbar-light bg-white shadow-sm px-3 d-flex justify-content-between align-items-center">
                <button class="btn btn-outline-secondary d-md-none" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <span class="fw-semibold">{{ $title ?? 'Dashboard' }}</span>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted d-none d-sm-block">{{ Auth::user()->name }}</span>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" class="rounded-circle" width="32" height="32" />
                </div>
            </nav>

            <!-- Page Content -->
            <main class="p-3">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    </script>
</body>
</html>
    