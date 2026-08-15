<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
    <!-- Header Page & Action Button -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Master Data Group & Lane Produksi</h2>
            <p class="text-slate-500 text-sm">Kelola daftar grup produksi, kategori, nomor lane, mesin, dan vendor subcont.</p>
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

    <!-- Data Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle border-slate-200 text-sm mb-0">
            <thead class="table-light text-slate-700">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Grup</th>
                    <th>Category</th>
                    <th>No Lane / MC / Vendor</th>
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
                        <tr>
                            <td><?= $index + 1 ?></td>
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
                        <option value="OutPlant">OutPlant</option>
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
        <form action="/lanes/import" method="POST" enctype="multipart/form-data">
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

<!-- JAVASCRIPT SYSTEM & DYNAMIC DROPDOWN -->
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

        // Reset opsi category
        categorySelect.innerHTML = '<option value="">-- Pilih Category --</option>';

        if (selectedGroup && categoryMapping[selectedGroup]) {
            categorySelect.disabled = false;

            categoryMapping[selectedGroup].forEach(cat => {
                const option = document.createElement('option');
                option.value = cat;
                option.textContent = cat;
                if (cat === selectedCategory) {
                    option.selected = true;
                }
                categorySelect.appendChild(option);
            });

            // Jika hanya 1 pilihan category, otomatis pilih secara instan
            if (categoryMapping[selectedGroup].length === 1 && !selectedCategory) {
                categorySelect.value = categoryMapping[selectedGroup][0];
            }
        } else {
            categorySelect.disabled = true;
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

        // Update dropdown category di modal Edit sesuai grup dan otomatis terpilih category-nya
        updateCategoryDropdown('edit', lane.category_name || '');

        openModal('modalEditLane');
    }

    function confirmDelete(id, laneName) {
        if (confirm(`Yakin ingin menghapus Lane/MC '${laneName}'?`)) {
            document.getElementById('delete_lane_id').value = id;
            document.getElementById('formDeleteLane').submit();
        }
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
</script>