<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ASN Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.4.1/css/rowGroup.dataTables.min.css">
    <style>
        :root {
            --primary: #0b2545;
            --primary-2: #0e3a66;
            --secondary: #0b58a6;
            --accent: #03a9f4;
            --success: #27ae60;
            --danger: #e74c3c;
            --muted: #6b7280;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f6f9;
        }

        /* Sidebar base */
        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-2) 100%);
            min-height: 100vh;
            color: white;
            padding: 20px 0;
        }

        /* Professional sidebar/menu styles */
        .sidebar .navbar-brand {
            font-size: 18px;
            font-weight: 800;
            color: #ffffff;
            padding-left: 18px;
            padding-bottom: 12px;
            letter-spacing: 0.4px;
        }

        .sidebar .nav-label {
            color: rgba(230,238,248,0.75);
            font-size: 12px;
            padding: 12px 18px 6px;
            text-transform: uppercase;
            font-weight: 700;
        }

        /* Reduced font-size for sidebar items to avoid overly large labels */
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(230,238,248,0.95);
            padding: 8px 14px;
            margin: 6px 12px;
            border-radius: 8px;
            transition: all .18s ease-in-out;
            font-weight: 600;
            text-decoration: none;
            font-size: 14px; /* adjusted smaller */
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 14px;
        }

        .sidebar .nav-link:hover {
            transform: translateY(-1px);
            background: rgba(255,255,255,0.04);
            color: #fff;
            text-decoration: none;
        }

        /* Active highlight */
        .sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(11,88,166,0.95) 0%, rgba(3,155,216,0.95) 100%);
            color: #fff !important;
            box-shadow: 0 8px 24px rgba(3, 103, 162, 0.14);
            border-left: 4px solid rgba(255,255,255,0.12);
        }

        .sidebar .nav-link.active .fa {
            color: rgba(255,255,255,0.95);
        }

        /* content & cards */
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
            border-radius: 8px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
            color: white;
            border: none;
        }

        .btn-primary {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

        .btn-primary:hover {
            background-color: #0a4f94;
            border-color: #0a4f94;
        }

        .kpi-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: 15px;
        }

        .kpi-card h3 {
            color: var(--muted);
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
            background-color: #f2f6fb;
            color: var(--primary);
        }

        .dropdown-item.text-danger:hover {
            background-color: #fadbd8;
        }

        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                padding-bottom: 10px;
            }
            .sidebar .nav-link {
                margin: 4px 8px;
                padding: 8px 10px;
                font-size: 13px;
            }
            .sidebar .nav-label { padding-left: 10px; }
        }
    </style>

    @yield('css')
</head>
<body>
    <div class="container-fluid">
        <div class="row" style="min-height: 100vh;">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="navbar-brand ps-3 pb-2">
                    BKPSDM CIANJUR
                </div>

                <div class="nav-label mt-3">MASTER DATA</div>
                <a href="{{ route('jabatan.index') }}" class="nav-link @if(Request::routeIs('jabatan.*')) active @endif">
                    <i class="fas fa-briefcase"></i> <span>Jabatan</span>
                </a>

                <div class="nav-label mt-3">PERENCANAAN</div>
                {{-- Formasi ASN: aktif hanya bila route formasi.* kecuali formasi.yearly-plan --}}
                <a href="{{ route('formasi.index') }}" class="nav-link @if(Request::routeIs('formasi.*') && !Request::routeIs('formasi.yearly-plan')) active @endif">
                    <i class="fas fa-calendar-alt"></i> <span>Formasi ASN</span>
                </a>
                {{-- Rencana Tahunan: aktif bila route yearly-plan --}}
                <a href="{{ route('formasi.yearly-plan') }}" class="nav-link @if(Request::routeIs('formasi.yearly-plan')) active @endif">
                    <i class="fas fa-chart-bar"></i> <span>Rencana Tahunan</span>
                </a>

                <div class="nav-label mt-3">LAPORAN</div>
                <a href="{{ route('laporan.gap-analysis') }}" class="nav-link @if(Request::routeIs('laporan.gap-analysis')) active @endif">
                    <i class="fas fa-chart-line"></i> <span>Analisis</span>
                </a>
                <a href="{{ route('laporan.summary') }}" class="nav-link @if(Request::routeIs('laporan.summary')) active @endif">
                    <i class="fas fa-file-alt"></i> <span>Ringkasan</span>
                </a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4" style="border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
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

    <!-- Standard JS assets -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.4.1/js/dataTables.rowGroup.min.js"></script>
    
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
    {{-- render pushed scripts (Vite / view-specific scripts) --}}
    @stack('scripts')

</body>
</html>