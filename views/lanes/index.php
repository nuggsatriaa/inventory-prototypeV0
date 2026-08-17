<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
    <!-- Header Page & Action Button -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Master Lane Produksi & SubCont</h2>
            <p class="text-slate-500 text-sm">Kelola daftar kategori, nomor lane dan mesin</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="btn btn-success bg-emerald-600 hover:bg-emerald-700 text-white border-none flex items-center gap-2 px-3 py-2 text-sm rounded-lg shadow-sm cursor-pointer" onclick="openModal('modalImportLane')">
                <i class="bi bi-file-earmark-excel"></i> Import Excel
            </button>
            <button type="button" class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white border-none flex items-center gap-2 px-3 py-2 text-sm rounded-lg shadow-sm cursor-pointer" onclick="openAddModal()">
                <i class="bi bi-plus-lg"></i> Tambah Lane Baru
            </button>
        </div>
    </div>

    <!-- allert -->
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> Data Master Lane berhasil di-import.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] === 'error_upload'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> Terjadi kesalahan saat mengunggah file Excel.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <!-- TABLE CONTROLS (FILTER & ROW LIMIT) -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4 text-xs font-semibold text-slate-600">
        <!-- Limit Row Per Page -->
        <div class="flex items-center gap-2">
            <span>Tampilkan</span>
            <select id="rowLimit" onchange="updateTable()" class="form-select form-select-sm w-auto text-xs rounded-md border-slate-300">
                <option value="10" selected>10</option>
                <option value="30">30</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span>data</span>
        </div>

        <!-- Filter Search Box -->
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <span>Cari:</span>
            <input type="text" id="searchInput" onkeyup="updateTable()" placeholder="Ketik kata kunci..." class="form-control form-control-sm w-full sm:w-64 text-xs rounded-md border-slate-300">
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
        <table id="customTable" class="table table-hover align-middle border-slate-200 text-sm mb-0">
            <thead class="table-light text-slate-700">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Grup</th>
                    <th>Category</th>
                    <th>No Lane</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lanes)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-6 text-slate-400">
                            Belum ada data lane. Klik <strong>Tambah Lane Baru</strong> atau <strong>Import Excel</strong> di atas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($lanes as $index => $lane): ?>
                        <tr class="table-row-data">
                            <td class="row-number"><?= $index + 1 ?></td>
                            <td>
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-md bg-purple-50 text-purple-700 border border-purple-200">
                                    <?= htmlspecialchars($lane['group_name'] ?? '-') ?>
                                </span>
                            </td>
                            <td>
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                                    <?= htmlspecialchars($lane['category_name'] ?? '-') ?>
                                </span>
                            </td>
                            <td>
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                                    <?= htmlspecialchars($lane['lane_name'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-warning me-1 px-2 py-1"
                                    onclick='openEditModal(<?= json_encode($lane, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1"
                                    onclick="confirmDelete(<?= $lane['id'] ?>, '<?= htmlspecialchars($lane['lane_name'] ?? '') ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION FOOTER CONTROLS -->
    <div class="flex flex-col sm:flex-row justify-between items-center mt-4 text-xs text-slate-500 gap-2">
        <div id="tableInfo">Menampilkan 0 data</div>
        <div class="flex items-center gap-2" id="paginationControls">
            <button id="prevBtn" onclick="changePage(-1)" class="btn btn-sm btn-light border text-xs px-3">Prev</button>
            <span id="pageIndicator" class="px-2 font-medium text-slate-700">Halaman 1</span>
            <button id="nextBtn" onclick="changePage(1)" class="btn btn-sm btn-light border text-xs px-3">Next</button>
        </div>
    </div>
</div>

<!-- ==================== ALL MODALS (PURE TAILWIND & JS) ==================== -->

<!-- 1. MODAL TAMBAH LANE -->
<div id="modalAddLane" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-150">
        <form action="/lanes/store" method="POST">
            <div class="flex justify-between items-center p-4 border-b border-slate-200 bg-slate-50">
                <h5 class="font-bold text-slate-800">Tambah Group / Lane Baru</h5>
                <button type="button" onclick="closeModal('modalAddLane')" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2">&times;</button>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label class="form-label text-xs font-bold text-slate-600">Grup</label>
                    <select id="add_group_name" name="group_name" class="form-select form-select-sm" onchange="updateCategoryDropdown('add')" required>
                        <option value="">-- Pilih Grup --</option>
                        <option value="PP1">PP1</option>
                        <option value="PP2">PP2</option>
                        <option value="PP3">PP3</option>
                        <option value="LA1">LA1</option>
                        <option value="LA2">LA2</option>
                        <option value="SMT">SMT</option>
                        <option value="OutPlant">OutPlant</option>
                    </select>
                </div>

                <div>
                    <label class="form-label text-xs font-bold text-slate-600">Category</label>
                    <select id="add_category_name" name="category_name" class="form-select form-select-sm" required disabled>
                        <option value="">-- Pilih Grup Terlebih Dahulu --</option>
                    </select>
                </div>

                <div>
                    <label class="form-label text-xs font-bold text-slate-600">No Lane / MC / Vendor</label>
                    <input type="text" name="lane_name" class="form-control form-control-sm" required placeholder="Contoh: PM62 / HASURA / Line 01">
                </div>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t border-slate-100 bg-slate-50">
                <button type="button" onclick="closeModal('modalAddLane')" class="btn btn-sm btn-secondary">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary bg-blue-600 text-white">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL EDIT LANE -->
<div id="modalEditLane" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <form action="/lanes/update" method="POST">
            <input type="hidden" name="id" id="edit_lane_id_pk">

            <div class="flex justify-between items-center p-4 border-b border-slate-200 bg-slate-50">
                <h5 class="font-bold text-slate-800">Edit Group / Lane</h5>
                <button type="button" onclick="closeModal('modalEditLane')" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2">&times;</button>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label class="form-label text-xs font-bold text-slate-600">Grup</label>
                    <select name="group_name" id="edit_group_name" class="form-select form-select-sm" onchange="updateCategoryDropdown('edit')" required>
                        <option value="">-- Pilih Grup --</option>
                        <option value="PP1">PP1</option>
                        <option value="PP2">PP2</option>
                        <option value="PP3">PP3</option>
                        <option value="LA1">LA1</option>
                        <option value="LA2">LA2</option>
                        <option value="SMT">SMT</option>
                    </select>
                </div>

                <div>
                    <label class="form-label text-xs font-bold text-slate-600">Category</label>
                    <select name="category_name" id="edit_category_name" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Category --</option>
                    </select>
                </div>

                <div>
                    <label class="form-label text-xs font-bold text-slate-600">No Lane / MC / Vendor</label>
                    <input type="text" name="lane_name" id="edit_lane_name" class="form-control form-control-sm" required>
                </div>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t border-slate-100 bg-slate-50">
                <button type="button" onclick="closeModal('modalEditLane')" class="btn btn-sm btn-secondary">Batal</button>
                <button type="submit" class="btn btn-sm btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. MODAL IMPORT EXCEL -->
<div id="modalImportLane" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <form action="/groups-lanes/import" method="POST" enctype="multipart/form-data">
            <div class="flex justify-between items-center p-4 border-b border-slate-200 bg-slate-50">
                <h5 class="font-bold text-slate-800">Import Master Lane dari Excel</h5>
                <button type="button" onclick="closeModal('modalImportLane')" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2">&times;</button>
            </div>
            <div class="p-4 space-y-3">
                <p class="text-xs text-slate-500">
                    Format kolom Excel: <br>
                    <strong>Col A:</strong> Grup | <strong>Col B:</strong> Category | <strong>Col C:</strong> No Lane / MC / Vendor
                </p>
                <div>
                    <label class="form-label text-xs font-bold text-slate-600">Pilih File Excel (.xlsx / .xls)</label>
                    <input type="file" name="excel_file" class="form-control form-control-sm" accept=".xlsx, .xls" required>
                </div>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t border-slate-100 bg-slate-50">
                <button type="button" onclick="closeModal('modalImportLane')" class="btn btn-sm btn-secondary">Batal</button>
                <button type="submit" class="btn btn-sm btn-success">Upload & Import</button>
            </div>
        </form>
    </div>
</div>

<!-- FORM DELETE HIDDEN -->
<form id="formDeleteLane" action="/lanes/delete" method="POST" class="d-none">
    <input type="hidden" name="id" id="delete_lane_id">
</form>

<!-- JAVASCRIPT SYSTEM & DYNAMIC DROPDOWN & PAGINATION -->
<script>
    // Pemetaan Relasi Grup -> Category
    const categoryMapping = {
        'PP1': ['Injection', 'Surface Treatment'],
        'PP2': ['Injection', 'Surface Treatment'],
        'PP3': ['Injection', 'Surface Treatment'],
        'LA1': ['Assy 2W'],
        'LA2': ['Assy 4W'],
        'SMT': ['SMT'],
        'OutPlant': ['SubCont']
    };

    function updateCategoryDropdown(mode, selectedCategory = '') {
        const groupSelect = document.getElementById(`${mode}_group_name`);
        const categorySelect = document.getElementById(`${mode}_category_name`);
        const selectedGroup = groupSelect.value;

        categorySelect.innerHTML = '';

        if (selectedGroup && categoryMapping[selectedGroup]) {
            categorySelect.removeAttribute('disabled');

            categorySelect.innerHTML = '<option value="">-- Pilih Category --</option>';
            categoryMapping[selectedGroup].forEach(cat => {
                const option = document.createElement('option');
                option.value = cat;
                option.textContent = cat;
                if (cat === selectedCategory) {
                    option.selected = true;
                }
                categorySelect.appendChild(option);
            });

            if (!selectedCategory && categoryMapping[selectedGroup].length === 1) {
                categorySelect.value = categoryMapping[selectedGroup][0];
            }
        } else {
            categorySelect.setAttribute('disabled', 'disabled');
            categorySelect.innerHTML = '<option value="">-- Pilih Grup Terlebih Dahulu --</option>';
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
        document.getElementById('add_group_name').value = '';
        updateCategoryDropdown('add');
        openModal('modalAddLane');
    }

    function openEditModal(lane) {
        document.getElementById('edit_lane_id_pk').value = lane.id;
        document.getElementById('edit_group_name').value = lane.group_name || '';
        document.getElementById('edit_lane_name').value = lane.lane_name || '';

        updateCategoryDropdown('edit', lane.category_name || '');

        openModal('modalEditLane');
    }

    function confirmDelete(id, laneName) {
        if (confirm(`Yakin ingin menghapus Lane/MC '${laneName}'?`)) {
            document.getElementById('delete_lane_id').value = id;
            document.getElementById('formDeleteLane').submit();
        }
    }

    // JS PAGINATION & SEARCH CONTROL
    let currentPage = 1;

    function updateTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const limit = parseInt(document.getElementById('rowLimit').value);
        const rows = Array.from(document.querySelectorAll('#customTable tbody tr.table-row-data'));

        if (rows.length === 0) return;

        // Filter pencarian berdasarkan teks baris
        const filteredRows = rows.filter(row => {
            const text = row.innerText.toLowerCase();
            return text.includes(searchInput);
        });

        // Hitung total halaman
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / limit) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // Sembunyikan semua baris
        rows.forEach(row => row.style.display = 'none');

        // Tampilkan hanya baris pada halaman aktif
        const start = (currentPage - 1) * limit;
        const end = start + limit;
        const pageRows = filteredRows.slice(start, end);

        pageRows.forEach((row, idx) => {
            row.style.display = '';
            const numCell = row.querySelector('.row-number');
            if (numCell) numCell.innerText = start + idx + 1;
        });

        // Update Info & Status Tombol
        const displayStart = totalRows > 0 ? start + 1 : 0;
        const displayEnd = Math.min(end, totalRows);

        const tableInfo = document.getElementById('tableInfo');
        const pageIndicator = document.getElementById('pageIndicator');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (tableInfo) tableInfo.innerText = `Menampilkan ${displayStart} sampai ${displayEnd} dari ${totalRows} data`;
        if (pageIndicator) pageIndicator.innerText = `Halaman ${currentPage} dari ${totalPages}`;

        if (prevBtn) prevBtn.disabled = (currentPage === 1);
        if (nextBtn) nextBtn.disabled = (currentPage === totalPages || totalRows === 0);
    }

    function changePage(direction) {
        currentPage += direction;
        updateTable();
    }

    // Close modal jika user klik backdrop
    window.onclick = function(event) {
        ['modalAddLane', 'modalEditLane', 'modalImportLane'].forEach(id => {
            const modal = document.getElementById(id);
            if (event.target === modal) {
                closeModal(id);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data Master Lane berhasil di-import.',
                timer: 2000,
                showConfirmButton: false
            });
        } else if (status === 'error_upload') {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Upload!',
                text: 'Terjadi kesalahan saat mengunggah file Excel.',
            });
        }

        // Hapus query parameter dari URL agar notifikasi tidak muncul lagi saat di-refresh
        window.history.replaceState({}, document.title, window.location.pathname);
    });

    document.addEventListener('DOMContentLoaded', updateTable);
</script>