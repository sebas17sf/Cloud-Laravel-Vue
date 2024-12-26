<!DOCTYPE html>
<html>
<head>
    <title>Gestión de VMs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href={{ route('vms.index') }}>VM Manager</a>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
            </ul>
    </nav>
    <div class="container mt-4">
        @yield('content')
    </div>
</body>
</html>
