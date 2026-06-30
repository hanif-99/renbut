<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ASN Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ecf0f1;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary) 0%, #34495e 100%);
            min-height: 100vh;
            color: white;
            padding: 20px 0;
        }

        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: var(--secondary);
        }

        .sidebar .nav-label {
            color: #95a5a6;
            font-size: 12px;
            padding: 15px 20px 5px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .navbar-brand {
            font-size: 18px;
            font-weight: 700;
            color: white;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, #34495e 100%);
            color: white;
            border: none;
        }

        .btn-primary {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .kpi-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .kpi-card h3 {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .kpi-card .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--secondary);
        }

        .table-responsive {
            border-radius: 8px;
        }

        .user-menu {
            position: relative;
        }

        .user-menu .dropdown-menu {
            right: 0;
            left: auto;
            min-width: 200px;
        }

        .dropdown-item {
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .dropdown-item:hover {
            background-color: #ecf0f1;
            color: var(--primary);
        }

        .dropdown-item.text-danger:hover {
            background-color: #fadbd8;
        }

        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
        }
    </style>
    @yield('css')
</head>
<body>
    <div class="container-fluid">
        <div class="row" style="min-height: 100vh;">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="navbar-brand ps-3 pb-4">
                    BKPSDM CIANJUR
                </div>

                <div class="nav-label">MENU UTAMA</div>
                <a href="{{ route('dashboard') }}" class="@if(Route::currentRouteName() == 'dashboard') active @endif">
                    <i class="fas fa-home"></i> Dashboard
                </a>

                <div class="nav-label mt-3">MASTER DATA</div>
                <a href="{{ route('perangkat_daerah.index') }}" class="@if(Route::currentRouteName() == 'perangkat_daerah.index' || Route::currentRouteName() == 'perangkat_daerah.create' || Route::currentRouteName() == 'perangkat_daerah.edit') active @endif">
                    <i class="fas fa-building"></i> Perangkat Daerah
                </a>
                <a href="{{ route('unit_organisasi.index') }}" class="@if(Route::currentRouteName() == 'unit_organisasi.index' || Route::currentRouteName() == 'unit_organisasi.create' || Route::currentRouteName() == 'unit_organisasi.edit') active @endif">
                    <i class="fas fa-sitemap"></i> Unit Organisasi
                </a>
                <a href="{{ route('jabatan.index') }}" class="@if(Route::currentRouteName() == 'jabatan.index' || Route::currentRouteName() == 'jabatan.create' || Route::currentRouteName() == 'jabatan.edit') active @endif">
                    <i class="fas fa-briefcase"></i> Jabatan
                </a>

                <div class="nav-label mt-3">PERENCANAAN</div>
                <a href="{{ route('formasi.index') }}" class="@if(Route::currentRouteName() == 'formasi.index' || Route::currentRouteName() == 'formasi.create' || Route::currentRouteName() == 'formasi.edit') active @endif">
                    <i class="fas fa-calendar-alt"></i> Formasi ASN
                </a>
                <a href="{{ route('formasi.yearly-plan') }}" class="@if(Route::currentRouteName() == 'formasi.yearly-plan') active @endif">
                    <i class="fas fa-chart-bar"></i> Rencana Tahunan
                </a>

                <div class="nav-label mt-3">LAPORAN</div>
                <a href="{{ route('laporan.summary') }}" class="@if(Route::currentRouteName() == 'laporan.summary') active @endif">
                    <i class="fas fa-file-alt"></i> Ringkasan
                </a>
                <a href="{{ route('laporan.gap-analysis') }}" class="@if(Route::currentRouteName() == 'laporan.gap-analysis') active @endif">
                    <i class="fas fa-chart-line"></i> Gap Analysis
                </a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4" style="border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <div class="ms-auto d-flex align-items-center gap-3">
                            <!-- User Dropdown -->
                            <div class="user-menu dropdown">
                                <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle"></i> 
                                    {{ Auth::user()->name ?? 'User' }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                    <li>
                                        <h6 class="dropdown-header">
                                            <i class="fas fa-user"></i> Akun
                                        </h6>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#!">
                                            <i class="fas fa-key"></i> Ganti Password
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger" style="border: none; background: none; text-align: left; width: 100%; cursor: pointer;">
                                                <i class="fas fa-sign-out-alt"></i> Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                </nav>

                <!-- Alert Messages -->
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                }
            });
        });

        // Delete confirmation
        function confirmDelete(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.form.submit();
                }
            });
        }
    </script>

    @yield('js')
</body>
</html>