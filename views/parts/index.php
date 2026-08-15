<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
    <!-- Header Page & Action Button -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Master Data Parts (ICS)</h2>
            <p class="text-slate-500 text-sm">Kelola daftar komponen, jenis part, dan lokasi group/lane.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="btn btn-success bg-emerald-600 hover:bg-emerald-700 text-white border-none flex items-center gap-2 px-3 py-2 text-sm rounded-lg" data-bs-toggle="modal" data-bs-target="#modalImportPart">
                <i class="bi bi-file-earmark-excel"></i> Import Excel
            </button>
            <button type="button" class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white border-none flex items-center gap-2 px-3 py-2 text-sm rounded-lg" data-bs-toggle="modal" data-bs-target="#modalAddPart">
                <i class="bi bi-plus-lg"></i> Tambah Part Baru
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle border-slate-200 text-sm mb-0">
            <thead class="table-light text-slate-700">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>ICS NO</th>
                    <th>Part Name</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>No Lane / MC</th>
                    <th>Min. Stock</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($parts)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-6 text-slate-400">
                            Belum ada data part. Klik <strong>Tambah Part Baru</strong> atau <strong>Import Excel</strong> di atas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($parts as $index => $part): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="font-semibold text-slate-800"><?= htmlspecialchars($part['part_number'] ?? $part['ics_no'] ?? '-') ?></td>
                            <td class="text-slate-700"><?= htmlspecialchars($part['part_name'] ?? '-') ?></td>

                            <!-- Badges Seragam & Rapi -->
                            <td>
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                    <?= htmlspecialchars($part['type'] ?? '-') ?>
                                </span>
                            </td>
                            <td>
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-md bg-purple-50 text-purple-700 border border-purple-200">
                                    <?= htmlspecialchars(!empty($part['category']) ? $part['category'] : '-') ?>
                                </span>
                            </td>
                            <td>
                                <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                                    <?= htmlspecialchars($part['group_code'] ?? $part['lane_mc'] ?? '-') ?>
                                </span>
                            </td>

                            <td class="text-slate-700"><?= number_format($part['min_stock'] ?? 0) ?> pcs</td>

                            <!-- Tombol Aksi -->
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-warning me-1 px-2 py-1"
                                    onclick='openEditModal(<?= json_encode($part, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1"
                                    onclick="confirmDelete(<?= $part['id'] ?>, '<?= htmlspecialchars($part['part_number'] ?? $part['ics_no'] ?? '') ?>')">
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

<!-- ==================== ALL MODALS ==================== -->

<!-- 1. MODAL TAMBAH PART -->
<div class="modal fade" id="modalAddPart" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/parts/store" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title font-bold text-slate-800">Tambah Part Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body space-y-3">
                    <div>
                        <label class="form-label text-xs font-bold text-slate-600">ICS NO (Part Number)</label>
                        <input type="text" name="part_number" class="form-control form-control-sm" required placeholder="Contoh: 1122334455">
                    </div>

                    <div>
                        <label class="form-label text-xs font-bold text-slate-600">Part Name</label>
                        <input type="text" name="part_name" class="form-control form-control-sm" required placeholder="Contoh: W67775M/HSG">
                    </div>

                    <div>
                        <label class="form-label text-xs font-bold text-slate-600">Type</label>
                        <input type="text" name="type" class="form-control form-control-sm" required placeholder="Contoh: K1ZG">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label class="form-label text-xs font-bold text-slate-600">Category</label>
                            <select name="category" id="add_category" class="form-select form-select-sm" onchange="updateLaneOptions('add')" required>
                                <option value="">-- Pilih Category --</option>
                                <option value="Injection">Injection</option>
                                <option value="Surface Treatment">Surface Treatment</option>
                                <option value="LA 1">LA 1</option>
                                <option value="LA 2">LA 2</option>
                                <option value="SMT">SMT</option>
                                <option value="Subcont">Subcont</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-xs font-bold text-slate-600">No Lane / MC</label>
                            <select name="group_code" id="add_group_code" class="form-select form-select-sm" required disabled>
                                <option value="">-- Pilih Category Dulu --</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-xs font-bold text-slate-600">Min. Stock</label>
                        <input type="number" name="min_stock" class="form-control form-control-sm" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary bg-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. MODAL EDIT PART -->
<div class="modal fade" id="modalEditPart" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/parts/update" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title font-bold text-slate-800">Edit Part</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body space-y-3">
                    <div>
                        <label class="form-label text-xs font-bold text-slate-600">ICS NO</label>
                        <input type="text" name="part_number" id="edit_part_number" class="form-control form-control-sm" required>
                    </div>
                    <div>
                        <label class="form-label text-xs font-bold text-slate-600">Part Name</label>
                        <input type="text" name="part_name" id="edit_part_name" class="form-control form-control-sm" required>
                    </div>
                    <div>
                        <label class="form-label text-xs font-bold text-slate-600">Type</label>
                        <input type="text" name="type" id="edit_type" class="form-control form-control-sm" required>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label class="form-label text-xs font-bold text-slate-600">Category</label>
                            <select name="category" id="edit_category" class="form-select form-select-sm" onchange="updateLaneOptions('edit')" required>
                                <option value="">-- Pilih Category --</option>
                                <option value="Injection">Injection</option>
                                <option value="Surface Treatment">Surface Treatment</option>
                                <option value="LA 1">LA 1</option>
                                <option value="LA 2">LA 2</option>
                                <option value="SMT">SMT</option>
                                <option value="Subcont">Subcont</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-xs font-bold text-slate-600">No Lane / MC</label>
                            <select name="group_code" id="edit_group_code" class="form-select form-select-sm" required disabled>
                                <option value="">-- Pilih Category Dulu --</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-xs font-bold text-slate-600">Min. Stock</label>
                        <input type="number" name="min_stock" id="edit_min_stock" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. MODAL IMPORT EXCEL -->
<div class="modal fade" id="modalImportPart" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/parts/import" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title font-bold text-slate-800">Import Master Part dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body space-y-3">
                    <p class="text-xs text-slate-500">
                        Format kolom Excel: <br>
                        <strong>Col A:</strong> ICS NO | <strong>Col B:</strong> Part Name | <strong>Col C:</strong> Type | <strong>Col D:</strong> Category | <strong>Col E:</strong> No Lane/MC | <strong>Col F:</strong> Min Stock
                    </p>
                    <div>
                        <label class="form-label text-xs font-bold text-slate-600">Pilih File Excel (.xlsx / .xls)</label>
                        <input type="file" name="excel_file" class="form-control form-control-sm" accept=".xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FORM DELETE HIDDEN -->
<form id="formDelete" action="/parts/delete" method="POST" class="d-none">
    <input type="hidden" name="id" id="delete_id">
</form>

<!-- JAVASCRIPT SYSTEM -->
<script>
    // Data Master Lane per Kategori
    const laneMaster = {
        'Injection': ['PM62', 'PM63', 'MC62', 'MC63'],
        'Surface Treatment': ['Hardcoat Line 1', 'Vacuum Line A'],
        'LA 1': ['Line 01', 'Line 02'],
        'LA 2': ['Line 03', 'Line 04'],
        'SMT': ['SMT Line A', 'SMT Line B'],
        'Subcont': ['HASURA', 'PT MITRA', 'PT STANLEY SUB']
    };

    function updateLaneOptions(prefix, selectedLane = '') {
        const categorySelect = document.getElementById(prefix + '_category');
        const laneSelect = document.getElementById(prefix + '_group_code');
        const selectedCategory = categorySelect.value;

        laneSelect.innerHTML = '<option value="">-- Pilih No Lane / MC --</option>';

        if (selectedCategory && laneMaster[selectedCategory]) {
            laneSelect.disabled = false;
            laneMaster[selectedCategory].forEach(lane => {
                const opt = document.createElement('option');
                opt.value = lane;
                opt.textContent = lane;
                if (lane === selectedLane) {
                    opt.selected = true;
                }
                laneSelect.appendChild(opt);
            });
        } else {
            laneSelect.disabled = true;
            laneSelect.innerHTML = '<option value="">-- Pilih Category Dulu --</option>';
        }
    }

    function openEditModal(part) {
        document.getElementById('edit_id').value = part.id;
        document.getElementById('edit_part_number').value = part.part_number || part.ics_no || '';
        document.getElementById('edit_part_name').value = part.part_name || '';
        document.getElementById('edit_type').value = part.type || '';
        document.getElementById('edit_min_stock').value = part.min_stock || 0;

        const category = part.category || '';
        const lane = part.group_code || part.lane_mc || '';

        document.getElementById('edit_category').value = category;
        updateLaneOptions('edit', lane);

        var modal = new bootstrap.Modal(document.getElementById('modalEditPart'));
        modal.show();
    }

    function confirmDelete(id, partNumber) {
        if (confirm(`Yakin ingin menghapus part '${partNumber}'?`)) {
            document.getElementById('delete_id').value = id;
            document.getElementById('formDelete').submit();
        }
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>