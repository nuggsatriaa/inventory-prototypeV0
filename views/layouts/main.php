<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Inventory System - PT Indonesia Stanley Electric' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: #0f172a;
            /* Dark Slate */
            color: #f8fafc;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            border-radius: 0.5rem;
            padding: 0.65rem 1rem;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease-in-out;
        }

        .sidebar .nav-link:hover {
            background-color: #1e293b;
            color: #38bdf8;
        }

        .sidebar .nav-link.active {
            background-color: #0284c7;
            color: #ffffff;
            font-weight: 600;
        }

        .main-content {
            flex: 1;
            min-width: 0;
        }
    </style>
</head>

<body class="flex min-h-screen">

    <!-- SIDEBAR NAVIGASI -->
    <aside class="sidebar flex flex-col justify-between p-4 shrink-0 shadow-lg">
        <div>
            <!-- Header Brand Stanley -->
            <div class="brand-header pb-4 mb-4 border-b border-slate-700 flex items-center gap-3">
                <div class="bg-blue-600 text-white font-bold p-2.5 rounded-lg text-lg leading-none shadow">
                    ISE
                </div>
                <div>
                    <h1 class="text-sm font-bold text-white tracking-wide uppercase">PT Stanley Electric</h1>
                    <p class="text-xs text-slate-400">Inventory Management</p>
                </div>
            </div>

            <!-- Menu Navigation -->
            <nav class="nav flex-column">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 px-3">Main Menu</div>

                <a href="/" class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill text-lg"></i> Dashboard
                </a>

                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider my-2 px-3">Master Data</div>

                <a href="/parts" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/parts') ? 'active' : '' ?>">
                    <i class="bi bi-box-seam-fill text-lg"></i> Master Parts (ICS)
                </a>

                <a href="/groups-lanes" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/groups-lanes') ? 'active' : '' ?>">
                    <i class="bi bi-diagram-3-fill text-lg"></i> Group & Lane Produksi
                </a>

                <a href="/customers" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/customers') ? 'active' : '' ?>">
                    <i class="bi bi-building-fill text-lg"></i> Data Customer
                </a>

                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider my-2 px-3">Transaksi Stok</div>

                <a href="/inventory/production" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/inventory/production') ? 'active' : '' ?>">
                    <i class="bi bi-arrow-left-right text-lg"></i> Stok Produksi
                </a>

                <a href="/inventory/whp" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/inventory/whp') ? 'active' : '' ?>">
                    <i class="bi bi-houses-fill text-lg"></i> Stok WHP
                </a>

                <a href="/single-part" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/single-part') ? 'active' : '' ?>">
                    <i class="bi bi-search text-lg"></i> Single Part (BOM)
                </a>
            </nav>
        </div>

        <!-- Footer Sidebar / User Info -->
        <div class="pt-4 border-t border-slate-700">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span>Versi Application 1.0</span>
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="main-content flex flex-col flex-1">

        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2 text-slate-600 font-medium text-sm">
                <i class="bi bi-clock-history text-blue-600"></i>
                <span>Shift Aktif: <strong class="text-blue-700 font-bold" id="liveShiftBadge">Loading...</strong></span>
            </div>

            <!-- Profile & Logout Dropdown -->
            <div class="dropdown">
                <button class="btn btn-light btn-sm dropdown-toggle flex items-center gap-2 border" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle text-base text-slate-600"></i>
                    <span class="text-sm font-semibold">NIK: 12345 (Admin)</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end text-sm shadow">
                    <li><a class="dropdown-item" href="/profile"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Keluar / Logout</a></li>
                </ul>
            </div>
        </header>

        <!-- DYNAMIC PAGE CONTENT -->
        <main class="p-6 flex-1">
            <?= $content ?>
        </main>

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/jszip/bootstrap.bundle.min.js"></script>

    <!-- Script Auto Shift Indicator -->
    <script>
        function updateShiftBadge() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const timeInMinutes = hours * 60 + minutes;

            let shiftText = "";

            // Shift 3: 00:00 - 07:45 (0 - 465 mins)
            // Shift 1: 07:46 - 16:45 (466 - 1005 mins)
            // Shift 2: 16:46 - 23:59 (1006 - 1439 mins)
            if (timeInMinutes >= 466 && timeInMinutes <= 1005) {
                shiftText = "SHIFT 1 (Pagi)";
            } else if (timeInMinutes >= 1006 && timeInMinutes <= 1439) {
                shiftText = "SHIFT 2 (Sore)";
            } else {
                shiftText = "SHIFT 3 (Malam)";
            }

            document.getElementById('liveShiftBadge').innerText = shiftText;
        }

        updateShiftBadge();
        setInterval(updateShiftBadge, 60000); // Update tiap 1 menit
    </script>
</body>

</html>