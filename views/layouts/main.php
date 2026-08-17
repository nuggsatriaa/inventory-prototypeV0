<?php
$uri = $_SERVER['REQUEST_URI'] ?? '';

// Cek status grup menu untuk auto-expand dropdown
$isMasterActive = str_contains($uri, '/parts') || str_contains($uri, '/groups-lanes') || str_contains($uri, '/subconts') || str_contains($uri, '/customers');
$isStockActive  = str_contains($uri, '/inventory') || str_contains($uri, '/single-part');
?>
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
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
        }

        .sidebar {
            width: 260px;
            background-color: #0f172a;
            color: #f8fafc;
            transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
        }

        /* State ketika Sidebar Tersembunyi */
        .sidebar.collapsed {
            margin-left: -260px;
        }

        .main-content {
            flex: 1;
            min-width: 0;
        }
    </style>
</head>

<body class="flex h-screen overflow-hidden bg-slate-100">

    <!-- SIDEBAR NAVIGASI (Toggleable & Independent Scroll) -->
    <aside id="sidebar" class="sidebar w-64 h-screen overflow-y-auto flex flex-col justify-between p-4 shrink-0 shadow-lg bg-slate-900 text-slate-300">
        <div>
            <!-- Header Brand Stanley -->
            <div class="brand-header pb-4 mb-4 border-b border-slate-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-600 text-white font-bold p-2.5 rounded-lg text-lg leading-none shadow">
                        ISE
                    </div>
                    <div>
                        <h1 class="text-sm font-bold text-white tracking-wide uppercase">PT Stanley Electric</h1>
                        <p class="text-xs text-slate-400">Digital Inventory System</p>
                    </div>
                </div>
            </div>

            <!-- Menu Navigation -->
            <nav class="nav flex flex-col gap-1">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 px-3">Main Menu</div>

                <a href="/" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors hover:bg-slate-800 text-sm font-medium <?= ($uri == '/' || $uri == '/dashboard') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-400' ?>">
                    <i class="bi bi-grid-1x2-fill text-lg"></i>
                    <span>Dashboard</span>
                </a>

                <!-- DROPDOWN 1: MASTER DATA -->
                <div class="mt-2">
                    <button type="button"
                        onclick="toggleSidebarMenu('menu-master', 'icon-master')"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition-colors hover:bg-slate-800 text-sm font-medium cursor-pointer text-slate-400 <?= $isMasterActive ? 'text-white font-bold' : '' ?>">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-database-fill text-lg text-blue-400"></i>
                            <span>Master Data</span>
                        </div>
                        <i id="icon-master" class="bi bi-chevron-down transition-transform duration-200 <?= $isMasterActive ? 'rotate-180' : '' ?>"></i>
                    </button>

                    <div id="menu-master" class="pl-6 space-y-1 mt-1 <?= $isMasterActive ? '' : 'hidden' ?>">
                        <a href="/parts" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors hover:bg-slate-800 text-slate-400 <?= str_contains($uri, '/parts') ? 'bg-blue-600/30 text-blue-400 font-bold border-l-2 border-blue-500' : '' ?>">
                            <i class="bi bi-box-seam-fill"></i> Master Parts
                        </a>
                        <a href="/groups-lanes" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors hover:bg-slate-800 text-slate-400 <?= str_contains($uri, '/groups-lanes') ? 'bg-blue-600/30 text-blue-400 font-bold border-l-2 border-blue-500' : '' ?>">
                            <i class="bi bi-diagram-3-fill"></i> Group & Lane Produksi
                        </a>
                        <a href="/subconts" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors hover:bg-slate-800 text-slate-400 <?= str_contains($uri, '/subconts') ? 'bg-blue-600/30 text-blue-400 font-bold border-l-2 border-blue-500' : '' ?>">
                            <i class="bi bi-truck-front-fill"></i> Data SubCont
                        </a>
                        <a href="/customers" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors hover:bg-slate-800 text-slate-400 <?= str_contains($uri, '/customers') ? 'bg-blue-600/30 text-blue-400 font-bold border-l-2 border-blue-500' : '' ?>">
                            <i class="bi bi-building-fill"></i> Data Customer
                        </a>
                    </div>
                </div>

                <!-- DROPDOWN 2: TRANSAKSI STOK -->
                <div class="mt-2">
                    <button type="button"
                        onclick="toggleSidebarMenu('menu-stok', 'icon-stok')"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition-colors hover:bg-slate-800 text-sm font-medium cursor-pointer text-slate-400 <?= $isStockActive ? 'text-white font-bold' : '' ?>">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-box-arrow-in-right text-lg text-emerald-400"></i>
                            <span>Stok</span>
                        </div>
                        <i id="icon-stok" class="bi bi-chevron-down transition-transform duration-200 <?= $isStockActive ? 'rotate-180' : '' ?>"></i>
                    </button>

                    <div id="menu-stok" class="pl-6 space-y-1 mt-1 <?= $isStockActive ? '' : 'hidden' ?>">
                        <a href="/inventory/production" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors hover:bg-slate-800 text-slate-400 <?= str_contains($uri, '/inventory/production') ? 'bg-blue-600/30 text-blue-400 font-bold border-l-2 border-blue-500' : '' ?>">
                            <i class="bi bi-arrow-left-right"></i> Stok Produksi
                        </a>
                        <a href="/inventory/whp" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors hover:bg-slate-800 text-slate-400 <?= str_contains($uri, '/inventory/whp') ? 'bg-blue-600/30 text-blue-400 font-bold border-l-2 border-blue-500' : '' ?>">
                            <i class="bi bi-houses-fill"></i> Stok WHP
                        </a>
                        <a href="/single-part" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs transition-colors hover:bg-slate-800 text-slate-400 <?= str_contains($uri, '/single-part') ? 'bg-blue-600/30 text-blue-400 font-bold border-l-2 border-blue-500' : '' ?>">
                            <i class="bi bi-search"></i> Single Part (BOM)
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Footer Sidebar -->
        <div class="pt-4 border-t border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span>Production Control Dept</span>
                <!-- <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span> -->
            </div>
            <div>
                <footer class="flex items-center justify-between text-xs text-slate-400">
                    &copy; <?= date('Y') ?> PT Stanley Electric Indonesia

                </footer>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="main-content flex flex-col flex-1 h-screen overflow-hidden">

        <!-- TOP NAVBAR (Dengan Tombol Toggle Sidebar) -->
        <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between shadow-sm shrink-0">
            <div class="flex items-center gap-4">
                <!-- Tombol Toggle Hide/Show Sidebar -->
                <button type="button" onclick="toggleSidebarHide()" class="text-slate-600 hover:text-slate-900 p-1 rounded-md transition-colors focus:outline-none">
                    <i class="bi bi-list text-2xl"></i>
                </button>

                <div class="flex items-center gap-2 text-slate-600 font-medium text-sm">
                    <i class="bi bi-clock-history text-blue-600"></i>
                    <span>Shift Aktif: <strong class="text-blue-700 font-bold" id="liveShiftBadge">Loading...</strong></span>
                </div>
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

        <!-- DYNAMIC PAGE CONTENT & STICKY FOOTER -->
        <main class="p-6 flex-1 overflow-y-auto flex flex-col justify-between">

            <!-- Area Halaman Utama -->
            <div class="w-full">
                <?= $content ?>
            </div>

        </main>

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- Script Interactive Layout & Sidebar -->
    <script>
        // Toggle Menyembunyikan / Menampilkan Sidebar
        function toggleSidebarHide() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }

        // Toggle Submenu Dropdown
        function toggleSidebarMenu(menuId, iconId) {
            const menu = document.getElementById(menuId);
            const icon = document.getElementById(iconId);

            if (menu) menu.classList.toggle('hidden');
            if (icon) icon.classList.toggle('rotate-180');
        }

        // Shift Indicator Auto Update
        function updateShiftBadge() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const timeInMinutes = hours * 60 + minutes;

            let shiftText = "";
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
        setInterval(updateShiftBadge, 60000);

        $(document).ready(function() {
            if ($('#dataTable').length) {
                $('#dataTable').DataTable({
                    "lengthMenu": [10, 30, 50, 100],
                    "pageLength": 10,
                    "language": {
                        "search": "Cari:",
                        "lengthMenu": "Tampilkan _MENU_ data",
                        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        "infoEmpty": "Data tidak ditemukan",
                        "zeroRecords": "Tidak ada data yang cocok",
                        "paginate": {
                            "first": "Awal",
                            "last": "Akhir",
                            "next": "Next",
                            "previous": "Prev"
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>