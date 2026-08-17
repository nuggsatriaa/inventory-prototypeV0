<?php

/** @var array $parts */
/** @var array $customers */
/** @var array $categories */
/** @var array $lanes */
/** @var array $subconts */
?>
<!-- Header Page & Action Button -->
<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Master Data Parts</h2>
            <p class="text-slate-500 text-sm">Kelola daftar part number, kategori, lane produksi, dan minimum stock</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Tombol Import Excel -->
            <button type="button" class="btn btn-success bg-emerald-600 hover:bg-emerald-700 text-white border-none flex items-center gap-2 px-3 py-2 text-sm rounded-lg shadow-sm cursor-pointer" onclick="openModal('modalImportPart')">
                <i class="bi bi-file-earmark-excel"></i> Import Excel
            </button>
            <!-- Tombol Tambah Part -->
            <button type="button" class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white border-none flex items-center gap-2 px-3 py-2 text-sm rounded-lg shadow-sm cursor-pointer" onclick="openAddModal()">
                <i class="bi bi-plus-lg"></i> Tambah Part Baru
            </button>
        </div>
    </div>

    <!-- TABLE CONTROLS -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4 text-xs font-semibold text-slate-600">
        <div class="flex items-center gap-2">
            <span>Tampilkan</span>
            <select id="rowLimit" onchange="updateTable()" class="px-2 py-1 text-xs rounded-md border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="10" selected>10</option>
                <option value="30">30</option>
                <option value="50">50</option>
            </select>
            <span>data</span>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <span>Cari:</span>
            <input type="text" id="searchInput" onkeyup="updateTable()" placeholder="ICS/Name Part/Type/Lane/Customer..." class="px-3 py-1 w-full sm:w-64 text-xs rounded-md border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table id="customTable" class="w-full text-left border-collapse text-sm">
            <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                <tr>
                    <th class="p-3" style="width: 50px;">No</th>
                    <th class="p-3">ICS NO</th>
                    <th class="p-3">Part Name</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">No Lane / MC</th>
                    <th class="p-3">Customer</th>
                    <th class="p-3">Min. Stock</th>
                    <th class="p-3 text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($parts)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-8 text-slate-400">
                            Belum ada data part. Klik <strong>Tambah Part Baru</strong> di atas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($parts as $index => $item): ?>
                        <tr class="table-row-data hover:bg-slate-50 transition-colors">
                            <td class="p-3 row-number text-slate-500"><?= $index + 1 ?></td>
                            <td class="p-3 font-bold text-slate-800"><?= htmlspecialchars($item['ics_no'] ?? '-') ?></td>
                            <td class="p-3 text-slate-700"><?= htmlspecialchars($item['part_name'] ?? '-') ?></td>
                            <td class="p-3 text-slate-600"><?= htmlspecialchars($item['type'] ?? '-') ?></td>
                            <td class="p-3 text-slate-600"><?= htmlspecialchars($item['category'] ?? '-') ?></td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-700 border border-slate-200">
                                    <?= htmlspecialchars($item['no_lane'] ?? $item['lane_name'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded bg-blue-50 text-blue-700 border border-blue-200">
                                    <?= htmlspecialchars($item['customer_code'] ?? $item['customer_name'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="p-3 font-semibold text-slate-700"><?= number_format($item['min_stock'] ?? 0) ?></td>
                            <td class="p-3 text-center">
                                <button type="button" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors me-1"
                                    onclick='openEditModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                    onclick="confirmDelete(<?= $item['id'] ?>, '<?= htmlspecialchars($item['part_name'] ?? '') ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION FOOTER -->
    <div class="flex flex-col sm:flex-row justify-between items-center mt-4 text-xs text-slate-500 gap-2">
        <div id="tableInfo">Menampilkan 0 data</div>
        <div class="flex items-center gap-2">
            <button id="prevBtn" onclick="changePage(-1)" class="px-3 py-1.5 bg-white border border-slate-300 rounded-md hover:bg-slate-50 disabled:opacity-50 text-xs">Prev</button>
            <span id="pageIndicator" class="px-2 font-medium text-slate-700">Halaman 1</span>
            <button id="nextBtn" onclick="changePage(1)" class="px-3 py-1.5 bg-white border border-slate-300 rounded-md hover:bg-slate-50 disabled:opacity-50 text-xs">Next</button>
        </div>
    </div>
</div>

<!-- ==================== MODAL TAMBAH PART ==================== -->
<div id="modalAddPart" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden border border-slate-100">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-4 border-b border-slate-200 bg-slate-50">
            <h5 class="font-bold text-slate-800">Tambah Part Baru</h5>
            <button type="button" onclick="closeModal('modalAddPart')" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2">&times;</button>
        </div>

        <form action="/parts/store" method="POST">
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Baris 1 -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">ICS NO</label>
                        <input type="text"
                            name="ics_no"
                            id="ics_no"
                            class="form-control form-control-sm"
                            placeholder="Maksimal 10 digit angka"
                            maxlength="10"
                            inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Part Name</label>
                        <input type="text" name="part_name" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Nama komponen" required>
                    </div>

                    <!-- Baris 2 -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Type</label>
                        <input type="text" name="type" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Contoh: Inner / Outer">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Category</label>
                        <select name="category" id="add_category" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white" required onchange="handleCategoryChange('add_category', 'add_source_id')">
                            <option value="">-- Pilih Category --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat ?>"><?= $cat ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Baris 3 -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Lane / MC / Subcont</label>
                        <select name="source_id" id="add_source_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                            <option value="">-- Pilih Category Terlebih Dahulu --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Customer</label>
                        <select name="customer_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                            <option value="">-- Pilih Customer --</option>
                            <?php if (!empty($customers)): ?>
                                <?php foreach ($customers as $cust): ?>
                                    <option value="<?= $cust['id'] ?>"><?= htmlspecialchars(($cust['customer_code'] ?? '') . ' - ' . ($cust['customer_name'] ?? $cust['name'] ?? '')) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Baris 4 -->
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Min. Stock</label>
                        <input type="number" name="min_stock" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" value="0" min="0">
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan/Batal -->
            <div class="flex justify-end gap-2 p-4 border-t border-slate-100 bg-slate-50">
                <button type="button" onclick="closeModal('modalAddPart')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-200 hover:bg-slate-300 rounded-lg transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== MODAL EDIT PART ==================== -->
<div id="modalEditPart" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden border border-slate-100">
        <div class="flex justify-between items-center p-4 border-b border-slate-200 bg-slate-50">
            <h5 class="font-bold text-slate-800">Edit Data Part</h5>
            <button type="button" onclick="closeModal('modalEditPart')" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2">&times;</button>
        </div>

        <form action="/parts/update" method="POST">
            <input type="hidden" name="id" id="edit_part_id">

            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">ICS NO</label>
                        <input type="text" name="ics_no" id="edit_ics_no" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Part Name</label>
                        <input type="text" name="part_name" id="edit_part_name" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Type</label>
                        <input type="text" name="type" id="edit_type" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Category</label>
                        <select name="category" id="edit_category" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white" onchange="handleCategoryChange('edit_category', 'edit_source_id')">
                            <option value="">-- Pilih Category --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat ?>"><?= $cat ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Lane / MC / Subcont</label>
                        <select name="source_id" id="edit_source_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                            <option value="">-- Pilih Category Terlebih Dahulu --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Customer</label>
                        <select name="customer_id" id="edit_customer_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                            <option value="">-- Pilih Customer --</option>
                            <?php foreach ($customers as $cust): ?>
                                <option value="<?= $cust['id'] ?>">
                                    <?= htmlspecialchars(($cust['customer_code'] ?? '') . ' - ' . ($cust['customer_name'] ?? '')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Min. Stock</label>
                        <input type="number" name="min_stock" id="edit_min_stock" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" min="0">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 p-4 border-t border-slate-100 bg-slate-50">
                <button type="button" onclick="closeModal('modalEditPart')" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-200 hover:bg-slate-300 rounded-lg transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition-colors shadow-sm">Update</button>
            </div>
        </form>
    </div>
</div>
<!-- MODAL IMPORT EXCEL (PURE TAILWIND & JS) -->
<div id="modalImportPart" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-150">
        <form action="/parts/import" method="POST" enctype="multipart/form-data">
            <div class="flex justify-between items-center p-4 border-b border-slate-200 bg-slate-50">
                <h5 class="font-bold text-slate-800">Import Master Part dari Excel</h5>
                <button type="button" onclick="closeModal('modalImportPart')" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2">&times;</button>
            </div>
            <div class="p-4 space-y-3">
                <p class="text-xs text-slate-500">
                    Format urutan kolom file Excel / CSV: <br>
                    <strong>Col A:</strong> ICS NO | <strong>Col B:</strong> Part Name | <strong>Col C:</strong> Type | <strong>Col D:</strong> Category | <strong>Col E:</strong> No Lane / MC | <strong>Col F:</strong> Min Stock
                </p>
                <div>
                    <label class="form-label text-xs font-bold text-slate-600">Pilih File Excel / CSV (.xlsx / .csv)</label>
                    <input type="file" name="excel_file" class="form-control form-control-sm" accept=".csv, .xlsx, .xls" required>
                </div>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t border-slate-100 bg-slate-50">
                <button type="button" onclick="closeModal('modalImportPart')" class="btn btn-sm btn-secondary">Batal</button>
                <button type="submit" class="btn btn-sm btn-success bg-emerald-600 hover:bg-emerald-700 text-white">Upload & Import</button>
            </div>
        </form>
    </div>
</div>
</div>

<!-- FORM DELETE HIDDEN -->
<form id="formDeletePart" action="/parts/delete" method="POST" class="hidden">
    <input type="hidden" name="id" id="delete_part_id">
</form>

<script>
    // Data pendukung dari backend
    const lanesGroupedData = <?= json_encode($lanes ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    const subcontsData = <?= json_encode($subconts ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    // Fungsi penangan perubahan Category -> Lane/Subcont Dynamic Options
    function handleCategoryChange(catSelectId, sourceSelectId, selectedValue = null) {
        const catValue = document.getElementById(catSelectId).value;
        const sourceSelect = document.getElementById(sourceSelectId);

        sourceSelect.innerHTML = '<option value="">-- Pilih Lane / MC / Subcont --</option>';

        if (!catValue) {
            sourceSelect.innerHTML = '<option value="">-- Pilih Category Terlebih Dahulu --</option>';
            return;
        }

        if (catValue === 'SUBCONT') {
            // Tampilkan pilihan subcont
            if (Array.isArray(subcontsData) && subcontsData.length > 0) {
                subcontsData.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.subcont_name || item.name;
                    if (selectedValue && String(item.id) === String(selectedValue)) {
                        opt.selected = true;
                    }
                    sourceSelect.appendChild(opt);
                });
            }
        } else {
            // Tampilkan pilihan Lane berdasarkan Category
            const lanesList = lanesGroupedData[catValue] || [];
            if (lanesList.length > 0) {
                lanesList.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.lane_name || item.name;
                    if (selectedValue && String(item.id) === String(selectedValue)) {
                        opt.selected = true;
                    }
                    sourceSelect.appendChild(opt);
                });
            }
        }
    }

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function openAddModal() {
        openModal('modalAddPart');
    }

    function openEditModal(part) {
        document.getElementById('edit_part_id').value = part.id;
        document.getElementById('edit_ics_no').value = part.ics_no || '';
        document.getElementById('edit_part_name').value = part.part_name || '';
        document.getElementById('edit_type').value = part.type || '';
        document.getElementById('edit_category').value = part.category || '';
        document.getElementById('edit_customer_id').value = part.customer_id || '';
        document.getElementById('edit_min_stock').value = part.min_stock || 0;

        // Render dynamic dropdown lane/subcont & tentukan pilihan
        handleCategoryChange('edit_category', 'edit_source_id', part.source_id);

        openModal('modalEditPart');
    }

    function confirmDelete(id, name) {
        if (confirm(`Yakin ingin menghapus Part '${name}'?`)) {
            document.getElementById('delete_part_id').value = id;
            document.getElementById('formDeletePart').submit();
        }
    }

    // Pagination & Search Script
    let currentPage = 1;

    function updateTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const limit = parseInt(document.getElementById('rowLimit').value);
        const rows = Array.from(document.querySelectorAll('#customTable tbody tr.table-row-data'));

        if (rows.length === 0) return;

        const filteredRows = rows.filter(row => row.innerText.toLowerCase().includes(searchInput));
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / limit) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        rows.forEach(row => row.style.display = 'none');

        const start = (currentPage - 1) * limit;
        const end = start + limit;
        const pageRows = filteredRows.slice(start, end);

        pageRows.forEach((row, idx) => {
            row.style.display = '';
            const numCell = row.querySelector('.row-number');
            if (numCell) numCell.innerText = start + idx + 1;
        });

        document.getElementById('tableInfo').innerText = `Menampilkan ${totalRows > 0 ? start + 1 : 0} sampai ${Math.min(end, totalRows)} dari ${totalRows} data`;
        document.getElementById('pageIndicator').innerText = `Halaman ${currentPage} dari ${totalPages}`;
        document.getElementById('prevBtn').disabled = (currentPage === 1);
        document.getElementById('nextBtn').disabled = (currentPage === totalPages || totalRows === 0);
    }

    function changePage(direction) {
        currentPage += direction;
        updateTable();
    }

    window.onclick = function(event) {
        ['modalAddPart', 'modalEditPart', 'modalImportPart'].forEach(id => {
            const modal = document.getElementById(id);
            if (event.target === modal) {
                closeModal(id);
            }
        });
    };

    // Handlers untuk Alert SweetAlert2 setelah Redirect
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data Master Part berhasil di-import.',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (status === 'error_upload') {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Upload!',
                text: 'Terjadi kesalahan saat mengunggah file Excel/CSV.',
            });
        }

        // Hapus parameter URL agar notifikasi tidak muncul berulang saat di-refresh
        window.history.replaceState({}, document.title, window.location.pathname);
    });

    document.addEventListener('DOMContentLoaded', updateTable);
</script>