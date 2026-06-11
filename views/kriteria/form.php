<?php require_once 'views/layouts/header.php'; ?>

<?php
$mode     = $data['mode'] ?? 'create';
$kriteria = $data['kriteria'] ?? null;
$error    = $data['error'] ?? '';

$is_edit  = ($mode === 'edit' && $kriteria);
$title    = $is_edit ? 'Edit Kriteria' : 'Tambah Kriteria';
$exclude_id = $is_edit ? $kriteria['id'] : '';
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold"><?= $title ?></h4>
        <p class="text-muted mb-0 small">
            <?= $is_edit ? 'Perbarui data kriteria penilaian.' : 'Tambah kriteria penilaian baru ke dalam sistem.' ?>
        </p>
    </div>
    <a href="?page=kriteria" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- Toast Notification -->
<?php if (!empty($error)): ?>
<div class="toast-notification toast-error" id="autoToast">
    <div class="toast-icon">
        <i class="bi bi-exclamation-triangle-fill"></i>
    </div>
    <span><?= htmlspecialchars($error) ?></span>
</div>
<script>
    setTimeout(function() {
        var toast = document.getElementById('autoToast');
        if (toast) {
            toast.classList.add('toast-hide');
            setTimeout(function() { toast.remove(); }, 400);
        }
    }, 3000);
</script>
<?php endif; ?>

<!-- Inline Duplicate Warning Styles -->
<style>
    .duplicate-warning {
        display: none;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        padding: 8px 12px;
        background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
        border: 1px solid #ffc107;
        border-radius: 8px;
        color: #856404;
        font-size: 0.82rem;
        font-weight: 500;
        animation: slideDown 0.3s ease-out;
        box-shadow: 0 2px 8px rgba(255, 193, 7, 0.15);
    }
    .duplicate-warning.show {
        display: flex;
    }
    .duplicate-warning i {
        font-size: 1rem;
        flex-shrink: 0;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .form-control.is-duplicate {
        border-color: #ffc107 !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
    }
    .form-control.is-valid-field {
        border-color: #198754 !important;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.15) !important;
    }
    .valid-feedback-custom {
        display: none;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        padding: 8px 12px;
        background: linear-gradient(135deg, #d1e7dd 0%, #badbcc 100%);
        border: 1px solid #198754;
        border-radius: 8px;
        color: #0f5132;
        font-size: 0.82rem;
        font-weight: 500;
        animation: slideDown 0.3s ease-out;
        box-shadow: 0 2px 8px rgba(25, 135, 84, 0.1);
    }
    .valid-feedback-custom.show {
        display: flex;
    }
    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<!-- Form Card -->
<div class="card border-0">
    <div class="card-body p-4">
        <form method="POST" id="kriteriaForm" action="?page=kriteria&action=<?= $is_edit ? 'update&id=' . $kriteria['id'] : 'store' ?>">
            <div class="row g-4">
                <!-- Kode Kriteria -->
                <div class="col-md-4">
                    <label for="kode" class="form-label fw-semibold small">Kode Kriteria <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="kode" name="kode" 
                           placeholder="Contoh: C1" maxlength="5"
                           value="<?= htmlspecialchars($kriteria['kode'] ?? '') ?>" required>
                    <div class="form-text" id="kode-help">Kode unik untuk kriteria (misal: C1, C2, dst)</div>
                    <div class="duplicate-warning" id="kode-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span id="kode-warning-text">Kode ini sudah digunakan!</span>
                    </div>
                    <div class="valid-feedback-custom" id="kode-valid">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Kode tersedia</span>
                    </div>
                </div>

                <!-- Nama Kriteria -->
                <div class="col-md-4">
                    <label for="nama_kriteria" class="form-label fw-semibold small">Nama Kriteria <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_kriteria" name="nama_kriteria"
                           placeholder="Contoh: Nilai Tes Seleksi"
                           value="<?= htmlspecialchars($kriteria['nama_kriteria'] ?? '') ?>" required>
                    <div class="form-text" id="nama-help">Nama lengkap kriteria penilaian</div>
                    <div class="duplicate-warning" id="nama-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span id="nama-warning-text">Nama kriteria ini sudah digunakan!</span>
                    </div>
                    <div class="valid-feedback-custom" id="nama-valid">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Nama kriteria tersedia</span>
                    </div>
                </div>

                <!-- Jenis Kriteria -->
                <div class="col-md-4">
                    <label for="jenis" class="form-label fw-semibold small">Jenis Kriteria <span class="text-danger">*</span></label>
                    <select class="form-select" id="jenis" name="jenis" required>
                        <option value="Benefit" <?= (($kriteria['jenis'] ?? 'Benefit') == 'Benefit') ? 'selected' : '' ?>>
                            Benefit (Semakin tinggi semakin baik)
                        </option>
                        <option value="Cost" <?= (($kriteria['jenis'] ?? '') == 'Cost') ? 'selected' : '' ?>>
                            Cost (Semakin rendah semakin baik)
                        </option>
                    </select>
                    <div class="form-text">Menentukan arah normalisasi pada SAW</div>
                </div>

                <!-- Bobot (readonly) -->
                <?php if ($is_edit): ?>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Bobot (AHP)</label>
                    <input type="text" class="form-control" disabled readonly
                           value="<?= ($kriteria['bobot'] !== null && $kriteria['bobot'] > 0) ? number_format($kriteria['bobot'], 6) : '— Belum dihitung —' ?>">
                    <div class="form-text">Bobot diisi otomatis setelah perhitungan AHP</div>
                </div>
                <?php endif; ?>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4" id="btnSubmit">
                    <i class="bi bi-<?= $is_edit ? 'check-lg' : 'plus-circle' ?> me-1"></i>
                    <?= $is_edit ? 'Simpan Perubahan' : 'Tambah Kriteria' ?>
                </button>
                <a href="?page=kriteria" class="btn btn-light px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<!-- Real-time Duplicate Check Script -->
<script>
(function() {
    const excludeId = '<?= $exclude_id ?>';
    const kodeInput = document.getElementById('kode');
    const namaInput = document.getElementById('nama_kriteria');
    const btnSubmit = document.getElementById('btnSubmit');

    // Track duplicate state
    let duplicateState = { kode: false, nama_kriteria: false };

    // Debounce helper
    function debounce(fn, delay) {
        let timer;
        return function() {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, arguments), delay);
        };
    }

    function updateSubmitButton() {
        const hasDuplicate = duplicateState.kode || duplicateState.nama_kriteria;
        btnSubmit.disabled = hasDuplicate;
    }

    function checkDuplicate(field, value, input, warningEl, validEl, warningTextEl) {
        // Reset state if empty
        if (!value.trim()) {
            input.classList.remove('is-duplicate', 'is-valid-field');
            warningEl.classList.remove('show');
            validEl.classList.remove('show');
            duplicateState[field] = false;
            updateSubmitButton();
            return;
        }

        const params = new URLSearchParams({
            page: 'kriteria',
            action: 'check_duplicate',
            field: field,
            value: value.trim()
        });
        if (excludeId) params.append('exclude_id', excludeId);

        fetch('?' + params.toString())
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    input.classList.add('is-duplicate');
                    input.classList.remove('is-valid-field');
                    warningEl.classList.add('show');
                    validEl.classList.remove('show');

                    const label = field === 'kode' ? 'Kode' : 'Nama kriteria';
                    warningTextEl.textContent = label + ' "' + value.trim() + '" sudah digunakan!';

                    duplicateState[field] = true;
                } else {
                    input.classList.remove('is-duplicate');
                    input.classList.add('is-valid-field');
                    warningEl.classList.remove('show');
                    validEl.classList.add('show');

                    duplicateState[field] = false;
                }
                updateSubmitButton();
            })
            .catch(() => {
                // On error, allow submit
                input.classList.remove('is-duplicate', 'is-valid-field');
                warningEl.classList.remove('show');
                validEl.classList.remove('show');
                duplicateState[field] = false;
                updateSubmitButton();
            });
    }

    // Kode field
    const debouncedKode = debounce(function() {
        checkDuplicate(
            'kode', kodeInput.value,
            kodeInput,
            document.getElementById('kode-warning'),
            document.getElementById('kode-valid'),
            document.getElementById('kode-warning-text')
        );
    }, 400);

    kodeInput.addEventListener('input', debouncedKode);
    kodeInput.addEventListener('blur', function() {
        checkDuplicate(
            'kode', kodeInput.value,
            kodeInput,
            document.getElementById('kode-warning'),
            document.getElementById('kode-valid'),
            document.getElementById('kode-warning-text')
        );
    });

    // Nama field
    const debouncedNama = debounce(function() {
        checkDuplicate(
            'nama_kriteria', namaInput.value,
            namaInput,
            document.getElementById('nama-warning'),
            document.getElementById('nama-valid'),
            document.getElementById('nama-warning-text')
        );
    }, 400);

    namaInput.addEventListener('input', debouncedNama);
    namaInput.addEventListener('blur', function() {
        checkDuplicate(
            'nama_kriteria', namaInput.value,
            namaInput,
            document.getElementById('nama-warning'),
            document.getElementById('nama-valid'),
            document.getElementById('nama-warning-text')
        );
    });

    // Prevent submit if duplicates exist
    document.getElementById('kriteriaForm').addEventListener('submit', function(e) {
        if (duplicateState.kode || duplicateState.nama_kriteria) {
            e.preventDefault();
            alert('Terdapat data duplikat! Silakan periksa kode dan nama kriteria.');
        }
    });
})();
</script>

<?php require_once 'views/layouts/footer.php'; ?>
