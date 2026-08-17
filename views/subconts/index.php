<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
    <!-- Header Page & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Master Data SubCont</h2>
            <p class="text-slate-500 text-sm">Kelola daftar nama vendor subcont dan kategori operasionalnya.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="btn btn-success bg-emerald-600 hover:bg-emerald-700 text-white border-none flex items-center gap-2 px-3 py-2 text-sm rounded-lg shadow-sm cursor-pointer" onclick="openModal('modalImportSubcont')">
                <i class="bi bi-file-earmark-excel"></i> Import Excel
            </button>
            <button type="button" class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white border-none flex items-center gap-2 px-3 py-2 text-sm rounded-lg shadow-sm cursor-pointer" onclick="openAddModal()">
                <i class="bi bi-plus-lg"></i> Tambah SubCont Baru
            </button>
        </div>
    </div>

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
            <input type="text" id="searchInput" onkeyup="updateTable()" placeholder="Ketik nama Vendor / Kategori" class="form-control form-control-sm w-full sm:w-64 text-xs rounded-md border-slate-300">
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
        <table id="customTable" class="table table-hover align-middle border-slate-200 text-sm mb-0">
            <thead class="table-light text-slate-700">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama SubCont</th>
                    <th>Category</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subconts)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-6 text-slate-400">
                            Belum ada data SubCont. Klik <strong>Tambah SubCont Baru</strong> atau <strong>Import Excel</strong> untuk menambahkan data.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subconts as $index => $item): ?>
                        <tr class="table-row-data">
                            <td class="row-number"><?= $index + 1 ?></td>
                            <td>
                                <span class="font-semibold text-slate-700">
                                    <?= htmlspecialchars($item['subcont_name'] ?? '-') ?>
                                </span>
                            </td>
                            <td>
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                                    <?= htmlspecialchars($item['category'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-warning me-1 px-2 py-1"
                                    onclick='openEditModal(<?= json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1"
                                    onclick="confirmDelete(<?= $item['id'] ?>, '<?= htmlspecialchars($item['subcont_name'] ?? '') ?>')">
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

<!-- ==================== MODALS ==================== -->

<!-- 1. MODAL TAMBAH SUBCONT -->
<div id="modalAddSubcont" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-150">
        <form action="/subconts/store" method="POST">
            <div class="flex justify-between items-center p-4 border-b border-slate-200 bg-slate-50">
                <h5 class="font-bold text-slate-800">Tambah SubCont Baru</h5>
                <button type="button" onclick="closeModal('modalAddSubcont')" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2">&times;</button>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label class="form-label text-xs font-bold text-slate-600">Nama SubCont</label>
                    <input type="text" name="subcont_name" class="form-control form-control-sm" required placeholder="Contoh: JSCREATIVE / HASURA">
                </div>

                <div>
                    <label class="form-label text-xs font-bold text-slate-600">Category</label>
                    <select name="category" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Category --</option>
                        <option value="Miko Plastik">Miko Plastik</option>
                        <option value="Miko Cord CP">Miko Cord CP</option>
                        <option value="Miko Material">Miko Material</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t border-slate-100 bg-slate-50">
                <button type="button" onclick="closeModal('modalAddSubcont')" class="btn btn-sm btn-secondary">Batal</button>
                <button type="submit" class="btn btn-sm btn-primary bg-blue-600 text-white">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL EDIT SUBCONT -->
<div id="modalEditSubcont" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <form action="/subconts/update" method="POST">
            <input type="hidden" name="id" id="edit_subcont_id">

            <div class="flex justify-between items-center p-4 border-b border-slate-200 bg-slate-50">
                <h5 class="font-bold text-slate-800">Edit Data SubCont</h5>
                <button type="button" onclick="closeModal('modalEditSubcont')" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2">&times;</button>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label class="form-label text-xs font-bold text-slate-600">Nama SubCont</label>
                    <input type="text" name="subcont_name" id="edit_subcont_name" class="form-control form-control-sm" required>
                </div>

                <div>
                    <label class="form-label text-xs font-bold text-slate-600">Category</label>
                    <select name="category" id="edit_category" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Category --</option>
                        <option value="Miko Plastik">Miko Plastik</option>
                        <option value="Miko Cord CP">Miko Cord CP</option>
                        <option value="Miko Material">Miko Material</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t border-slate-100 bg-slate-50">
                <button type="button" onclick="closeModal('modalEditSubcont')" class="btn btn-sm btn-secondary">Batal</button>
                <button type="submit" class="btn btn-sm btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. MODAL IMPORT EXCEL -->
<div id="modalImportSubcont" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <form action="/subconts/import" method="POST" enctype="multipart/form-data">
            <div class="flex justify-between items-center p-4 border-b border-slate-200 bg-slate-50">
                <h5 class="font-bold text-slate-800">Import Master SubCont dari Excel</h5>
                <button type="button" onclick="closeModal('modalImportSubcont')" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2">&times;</button>
            </div>
            <div class="p-4 space-y-3">
                <p class="text-xs text-slate-500">
                    Format kolom Excel: <br>
                    <strong>Col A:</strong> Nama SubCont | <strong>Col B:</strong> Category
                </p>
                <div>
                    <label class="form-label text-xs font-bold text-slate-600">Pilih File Excel (.xlsx / .xls)</label>
                    <input type="file" name="excel_file" class="form-control form-control-sm" accept=".xlsx, .xls" required>
                </div>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t border-slate-100 bg-slate-50">
                <button type="button" onclick="closeModal('modalImportSubcont')" class="btn btn-sm btn-secondary">Batal</button>
                <button type="submit" class="btn btn-sm btn-success">Upload & Import</button>
            </div>
        </form>
    </div>
</div>

<!-- FORM DELETE HIDDEN -->
<form id="formDeleteSubcont" action="/subconts/delete" method="POST" class="d-none">
    <input type="hidden" name="id" id="delete_subcont_id">
</form>

<!-- JAVASCRIPT SYSTEM & PAGINATION -->
<script>
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
        openModal('modalAddSubcont');
    }

    function openEditModal(subcont) {
        document.getElementById('edit_subcont_id').value = subcont.id;
        document.getElementById('edit_subcont_name').value = subcont.subcont_name || '';
        document.getElementById('edit_category').value = subcont.category || '';

        openModal('modalEditSubcont');
    }

    function confirmDelete(id, subcontName) {
        if (confirm(`Yakin ingin menghapus SubCont '${subcontName}'?`)) {
            document.getElementById('delete_subcont_id').value = id;
            document.getElementById('formDeleteSubcont').submit();
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
        ['modalAddSubcont', 'modalEditSubcont', 'modalImportSubcont'].forEach(id => {
            const modal = document.getElementById(id);
            if (event.target === modal) {
                closeModal(id);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', updateTable);
</script>