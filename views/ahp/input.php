<?php require_once 'views/layouts/header.php'; ?>

<?php
$kriteria_list  = $data['kriteria'] ?? [];
$perbandingan   = $data['perbandingan'] ?? [];
$success        = $data['success'] ?? '';
$error          = $data['error'] ?? '';
$n              = count($kriteria_list);
$ids            = array_column($kriteria_list, 'id');
?>

<style>
/* Hilangkan spinner pada input number */
input.ahp-input::-webkit-inner-spin-button,
input.ahp-input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input.ahp-input {
    -moz-appearance: textfield;
}
</style>

<!-- Toast Notification -->
<?php if (!empty($success) || !empty($error)): ?>
<div class="toast-notification <?= !empty($success) ? 'toast-success' : 'toast-error' ?>" id="autoToast">
    <div class="toast-icon">
        <i class="bi <?= !empty($success) ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?>"></i>
    </div>
    <span><?= htmlspecialchars(!empty($success) ? $success : $error) ?></span>
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

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">AHP — Input Matriks Perbandingan</h4>
        <p class="text-muted mb-0 small">Masukkan nilai perbandingan berpasangan antar kriteria menggunakan skala Saaty (1–9).</p>
    </div>
    <a href="?page=ahp_hasil" class="btn btn-outline-primary">
        <i class="bi bi-bar-chart me-1"></i> Lihat Hasil
    </a>
</div>

<!-- Info Skala Saaty -->
<div class="card border-0 mb-4">
    <div class="card-body py-3">
        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Keterangan Skala Saaty</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:100px;">Intensitas</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary px-2">1</span></td>
                        <td>Kedua elemen <strong>sama pentingnya</strong></td>
                    </tr>
                    <tr>
                        <td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary px-2">3</span></td>
                        <td>Elemen yang satu <strong>sedikit lebih penting</strong> dari elemen lainnya</td>
                    </tr>
                    <tr>
                        <td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary px-2">5</span></td>
                        <td>Elemen yang satu <strong>lebih penting</strong> dari elemen lainnya</td>
                    </tr>
                    <tr>
                        <td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary px-2">7</span></td>
                        <td>Satu elemen jelas <strong>sangat lebih penting</strong> dari elemen lainnya</td>
                    </tr>
                    <tr>
                        <td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary px-2">9</span></td>
                        <td>Satu elemen <strong>mutlak lebih penting</strong> dari elemen lainnya</td>
                    </tr>
                    <tr>
                        <td class="text-center"><span class="badge bg-secondary bg-opacity-10 text-secondary px-2">2, 4, 6, 8</span></td>
                        <td>Nilai-nilai antara dua pertimbangan yang berdekatan (<em>kompromi</em>)</td>
                    </tr>
                    <tr>
                        <td class="text-center"><span class="badge bg-warning bg-opacity-15 text-dark px-2">1/n</span></td>
                        <td>Kebalikan — ketik langsung <code>1/3</code>, <code>1/5</code>, dst. dan akan otomatis dikonversi ke desimal</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Form Matriks -->
<div class="card border-0">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 text-primary">
            <i class="bi bi-grid-3x3 me-2"></i> Matriks Perbandingan Berpasangan (<?= $n ?>×<?= $n ?>)
        </h6>
        <p class="text-muted small mb-0 mt-1">Isi sel manapun — sel kebalikannya akan terisi otomatis. Bisa ketik format <code>1/3</code> dan akan langsung dikonversi.</p>
    </div>
    <div class="card-body p-0">
        <form action="?page=ahp&action=store" method="POST" id="formAhp">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" id="matriksTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:140px;">Kriteria</th>
                            <?php foreach ($kriteria_list as $k): ?>
                                <th class="text-center" title="<?= htmlspecialchars($k['nama_kriteria']) ?>">
                                    <?= htmlspecialchars($k['kode']) ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 0; $i < $n; $i++): ?>
                        <tr>
                            <td class="fw-semibold text-center table-light">
                                <?= htmlspecialchars($kriteria_list[$i]['kode']) ?>
                                <div class="small text-muted fw-normal"><?= htmlspecialchars($kriteria_list[$i]['nama_kriteria']) ?></div>
                            </td>
                            <?php for ($j = 0; $j < $n; $j++): ?>
                                <?php
                                    $id_i = $ids[$i];
                                    $id_j = $ids[$j];
                                    $has_existing = isset($perbandingan[$id_i][$id_j]);
                                    $existing_val = $has_existing ? floatval($perbandingan[$id_i][$id_j]) : null;
                                    // Format: integer tampil bersih tanpa desimal
                                    $display_val = 1;
                                    if ($has_existing) {
                                        $display_val = (floor($existing_val) == $existing_val)
                                            ? intval($existing_val)
                                            : round($existing_val, 4);
                                    }
                                ?>
                                <td class="text-center p-1">
                                    <?php if ($i == $j): ?>
                                        <!-- Diagonal = 1 -->
                                        <input type="text" class="form-control form-control-sm text-center bg-light ahp-input"
                                               value="1" readonly
                                               style="width:90px; margin:0 auto;">
                                    <?php elseif ($i < $j): ?>
                                        <!-- Segitiga atas: hidden = presisi penuh (dikirim), visible = tampilan rapi -->
                                        <input type="hidden" name="perbandingan_<?= $id_i ?>_<?= $id_j ?>"
                                               id="hval_<?= $i ?>_<?= $j ?>"
                                               value="<?= $has_existing ? $existing_val : 1 ?>">
                                        <input type="text"
                                               class="form-control form-control-sm text-center ahp-input ahp-cell"
                                               id="cell_<?= $i ?>_<?= $j ?>"
                                               data-row="<?= $i ?>" data-col="<?= $j ?>" data-pos="upper"
                                               value="<?= $display_val ?>"
                                               style="width:90px; margin:0 auto;">
                                    <?php else: ?>
                                        <!-- Segitiga bawah — editable, tapi tidak di-submit -->
                                        <input type="text"
                                               class="form-control form-control-sm text-center ahp-input ahp-cell"
                                               id="cell_<?= $i ?>_<?= $j ?>"
                                               data-row="<?= $i ?>" data-col="<?= $j ?>" data-pos="lower"
                                               value="<?= $display_val ?>"
                                               style="width:90px; margin:0 auto;">
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-3 bg-light text-end border-top">
                <button type="button" class="btn btn-outline-secondary me-2" onclick="resetMatriks()">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-calculator me-1"></i> Hitung Bobot AHP
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var cells = document.querySelectorAll('.ahp-cell');
    cells.forEach(function(cell) {
        // Saat user selesai mengetik dan keluar dari input (blur)
        cell.addEventListener('blur', function() {
            handleCellInput(this);
        });
        // Juga saat user tekan Enter
        cell.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleCellInput(this);
                this.blur();
            }
        });
    });
});

/**
 * Parse input user — mendukung format:
 * - Angka biasa: 3, 5, 7.5
 * - Pecahan: 1/3, 1/5, 2/7
 */
function parseAhpValue(str) {
    str = str.trim();
    // Cek format pecahan: "1/3", "1/5", "2/7", dsb.
    var parts = str.split('/');
    if (parts.length === 2) {
        var num = parseFloat(parts[0]);
        var den = parseFloat(parts[1]);
        if (!isNaN(num) && !isNaN(den) && den !== 0) {
            return num / den;
        }
    }
    // Angka biasa
    var val = parseFloat(str);
    return isNaN(val) ? null : val;
}

/**
 * Handle input pada sebuah cell:
 * 1. Parse nilai (termasuk format 1/n → desimal)
 * 2. Tampilkan hasil desimal di cell ini
 * 3. Isi cell mirror dengan reciprocal
 */
function handleCellInput(cellEl) {
    var row = parseInt(cellEl.getAttribute('data-row'));
    var col = parseInt(cellEl.getAttribute('data-col'));
    var pos = cellEl.getAttribute('data-pos'); // "upper" atau "lower"
    var rawValue = cellEl.value;

    var parsedVal = parseAhpValue(rawValue);

    // Validasi: harus angka positif
    if (parsedVal === null || parsedVal <= 0) {
        parsedVal = 1;
    }

    // Tampilkan nilai rapi di cell ini
    cellEl.value = formatAhp(parsedVal);

    // Update hidden input presisi penuh (jika upper triangle)
    if (pos === 'upper') {
        var hiddenInput = document.getElementById('hval_' + row + '_' + col);
        if (hiddenInput) hiddenInput.value = parsedVal;
    }

    // Hitung reciprocal
    var reciprocal = 1 / parsedVal;

    // Cari cell mirror [col][row]
    var mirrorCell = document.getElementById('cell_' + col + '_' + row);
    if (mirrorCell) {
        mirrorCell.value = formatAhp(reciprocal);
    }

    // Update hidden input presisi penuh di mirror (jika mirror = upper triangle)
    var mirrorHidden = document.getElementById('hval_' + col + '_' + row);
    if (mirrorHidden) {
        // Mirror upper triangle menyimpan reciprocal dengan presisi penuh
        mirrorHidden.value = reciprocal;
    }
}

/**
 * Format angka AHP untuk tampilan:
 * - Angka bulat → tanpa desimal (9, 3, 1)
 * - Angka desimal → max 4 digit (0.3333, 0.2)
 */
function formatAhp(val) {
    var rounded = Math.round(val * 10000) / 10000;
    if (Number.isInteger(rounded)) {
        return rounded.toString();
    }
    return rounded.toString();
}

function resetMatriks() {
    if (confirm('Apakah Anda yakin ingin mereset semua nilai matriks ke 1 (sama penting)?')) {
        var cells = document.querySelectorAll('.ahp-cell');
        cells.forEach(function(cell) {
            cell.value = '1';
        });
        // Reset semua hidden inputs juga
        var hiddens = document.querySelectorAll('input[id^="hval_"]');
        hiddens.forEach(function(h) {
            h.value = '1';
        });
    }
}
</script>

<?php require_once 'views/layouts/footer.php'; ?>
