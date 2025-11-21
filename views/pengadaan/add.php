<?php
/**
 * File: views/pengadaan/add.php
 * Fungsi: Form Tambah Pengadaan (Multiple Items)
 * Menggunakan: SP sp_get_vendor_dropdown(), sp_get_barang_dropdown()
 */

require_once(__DIR__ . '/../../config/DBConnection.php');
session_start();

$db = new DBConnection();
$conn = $db->getConnection();

// Pastikan session user ada
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Ambil info user login
$username = $_SESSION['user']['username'] ?? 'Tidak diketahui';
$iduser   = $_SESSION['user']['iduser'] ?? '';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengadaan Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style/dashboard.css">
    <link rel="stylesheet" href="../../assets/style/table.css">
    <style>
        body { background: #f8fafc; display: flex; }
        .main-content { flex-grow: 1; margin-left: 250px; padding: 20px; }
        .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .info-box { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .cart-item { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #28a745; }
        .total-box { background: #fff3cd; padding: 20px; border-radius: 8px; border: 2px solid #ffc107; }
    </style>
</head>

<body>
<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main-content">
    <div class="container py-4">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>📦 Tambah Pengadaan Barang</h2>
                <a href="list.php" class="btn btn-secondary">🔙 Kembali</a>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="info-box">
                <strong>ℹ️ Informasi:</strong><br>
                • Pilih vendor dan tambahkan beberapa barang dalam 1 transaksi<br>
                • Subtotal, PPN (10%), dan Total dihitung otomatis oleh sistem<br>
                • Klik "Tambah Detail" untuk menambahkan barang ke daftar
            </div>

            <form method="POST" action="store.php" id="formPengadaan">
                <div class="row">
                    <!-- Pilih Vendor -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">🏢 Pilih Vendor *</label>
                        <select name="idvendor" id="idvendor" class="form-select" required>
                            <option value="">-- Pilih Vendor --</option>
                            <?php
                            $result = $conn->query("CALL sp_get_vendor_dropdown()");
                            if ($result) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['idvendor']}'>{$row['display_text']}</option>";
                                }
                                $result->free_result();
                                $conn->next_result();
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Petugas -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-person-fill"></i> Petugas *</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($username) ?>" readonly>
                        <input type="hidden" name="iduser" value="<?= htmlspecialchars($iduser) ?>">
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3">📝 Tambah Detail Barang</h5>

                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label">📦 Pilih Barang *</label>
                        <select id="selectBarang" class="form-select">
                            <option value="">-- Pilih Barang --</option>
                            <?php
                            $result = $conn->query("CALL sp_get_barang_dropdown()");
                            if ($result) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['idbarang']}' 
                                              data-nama='{$row['nama']}' 
                                              data-satuan='{$row['nama_satuan']}' 
                                              data-harga='{$row['harga']}'>
                                          {$row['display_text']}
                                      </option>";
                                }
                                $result->free_result();
                                $conn->next_result();
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">🔢 Jumlah *</label>
                        <input type="number" id="inputJumlah" class="form-control" min="1" placeholder="0">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">💵 Harga Satuan</label>
                        <input type="text" id="displayHarga" class="form-control" readonly placeholder="Rp 0">
                    </div>
                </div>

                <button type="button" class="btn btn-primary mb-3" id="btnTambahDetail">➕ Tambah Detail</button>

                <hr class="my-4">
                <h5 class="mb-3">📋 Daftar Barang yang Dipesan</h5>

                <div id="daftarBarangContainer">
                    <div class="alert alert-info text-center">Belum ada barang yang ditambahkan</div>
                </div>

                <div class="total-box mt-4">
                    <div class="row">
                        <div class="col-md-8"><h5>Total Pengadaan:</h5></div>
                        <div class="col-md-4 text-end">
                            <div class="mb-2"><small>Subtotal:</small> <strong id="displaySubtotal">Rp 0</strong></div>
                            <div class="mb-2"><small>PPN (10%):</small> <strong id="displayPPN">Rp 0</strong></div>
                            <hr>
                            <h4 class="text-warning" id="displayTotal">Rp 0</h4>
                        </div>
                    </div>
                </div>

                <!-- HIDDEN INPUTS -->
                <div id="hiddenInputs"></div>

                <!-- STATUS DEFAULT WAJIB P -->
                <input type="hidden" name="status" value="P">

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" name="simpan_pengadaan" class="btn btn-success btn-lg" id="btnSimpan" disabled>✅ Simpan Pengadaan</button>
                    <a href="list.php" class="btn btn-secondary btn-lg">❌ Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let cart = [];

    document.getElementById('selectBarang').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            const harga = parseInt(option.dataset.harga);
            document.getElementById('displayHarga').value = 'Rp ' + formatRupiah(harga);
        } else {
            document.getElementById('displayHarga').value = 'Rp 0';
        }
    });

    document.getElementById('btnTambahDetail').addEventListener('click', function() {
        const selectBarang = document.getElementById('selectBarang');
        const option = selectBarang.options[selectBarang.selectedIndex];
        const jumlah = parseInt(document.getElementById('inputJumlah').value);

        if (!option.value) return alert('Pilih barang terlebih dahulu!');
        if (!jumlah || jumlah < 1) return alert('Masukkan jumlah yang valid!');
        if (cart.find(item => item.idbarang === option.value)) return alert('Barang sudah ada di daftar!');

        const harga = parseInt(option.dataset.harga);
        const subtotal = harga * jumlah;

        cart.push({ idbarang: option.value, nama: option.dataset.nama, satuan: option.dataset.satuan, harga, jumlah, subtotal });
        document.getElementById('selectBarang').value = '';
        document.getElementById('inputJumlah').value = '';
        document.getElementById('displayHarga').value = 'Rp 0';
        renderCart();
        updateTotal();
    });

    function renderCart() {
        const container = document.getElementById('daftarBarangContainer');

        if (cart.length === 0) {
            container.innerHTML = '<div class="alert alert-info text-center">Belum ada barang yang ditambahkan</div>';
            document.getElementById('btnSimpan').disabled = true;
            return;
        }

        let html = '';
        cart.forEach((item, index) => {
            html += `
                <div class="cart-item">
                    <div class="row align-items-center">
                        <div class="col-md-4"><strong>${item.nama}</strong><br><small class="text-muted">${item.idbarang} • ${item.satuan}</small></div>
                        <div class="col-md-2"><small>Harga:</small><br><strong>Rp ${formatRupiah(item.harga)}</strong></div>
                        <div class="col-md-2"><small>Jumlah:</small><br>
                            <input type="number" class="form-control form-control-sm" value="${item.jumlah}" min="1" onchange="updateJumlah(${index}, this.value)">
                        </div>
                        <div class="col-md-3"><small>Subtotal:</small><br><strong class="text-success">Rp ${formatRupiah(item.subtotal)}</strong></div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">🗑️</button>
                        </div>
                    </div>
                </div>`;
        });

        container.innerHTML = html;
        document.getElementById('btnSimpan').disabled = false;
        updateHiddenInputs();
    }

    function updateJumlah(index, newJumlah) {
        const jumlah = parseInt(newJumlah);
        if (jumlah < 1) return alert('Jumlah minimal 1');
        cart[index].jumlah = jumlah;
        cart[index].subtotal = cart[index].harga * jumlah;
        renderCart();
        updateTotal();
    }

    function removeFromCart(index) {
        if (confirm('Hapus barang ini dari daftar?')) {
            cart.splice(index, 1);
            renderCart();
            updateTotal();
        }
    }

    function updateTotal() {
        const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
        const ppn = Math.round(subtotal * 0.10);
        const total = subtotal + ppn;

        document.getElementById('displaySubtotal').textContent = 'Rp ' + formatRupiah(subtotal);
        document.getElementById('displayPPN').textContent = 'Rp ' + formatRupiah(ppn);
        document.getElementById('displayTotal').textContent = 'Rp ' + formatRupiah(total);
    }

    function updateHiddenInputs() {
        document.getElementById('hiddenInputs').innerHTML = cart.map(item => `
            <input type="hidden" name="barang_id[]" value="${item.idbarang}">
            <input type="hidden" name="harga_satuan[]" value="${item.harga}">
            <input type="hidden" name="jumlah[]" value="${item.jumlah}">
        `).join('');
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }
</script>

</body>
</html>
