<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../classes/Penjualan.php");
$penjualanObj = new Penjualan();

$message = '';

// Ambil ID Penjualan dari URL
$idpenjualan = isset($_GET['id']) ? $_GET['id'] : null;

if (!$idpenjualan) {
    header("Location: add.php");
    exit();
}

// Cek apakah penjualan ada
$penjualan = $penjualanObj->getById($idpenjualan);
if (!$penjualan) {
    $_SESSION['error_message'] = "Penjualan tidak ditemukan!";
    header("Location: list.php");
    exit();
}

// Tampilkan pesan sukses jika ada
if (isset($_SESSION['success_message'])) {
    $message = "<div class='alert alert-success alert-dismissible fade show'>" . 
               htmlspecialchars($_SESSION['success_message']) . 
               "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    unset($_SESSION['success_message']);
}

// Tambahkan Detail Barang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_detail'])) {
    try {
        $idbarang = $_POST['idbarang'];
        $jumlah = $_POST['jumlah'];
        $harga_jual = $_POST['harga_jual'];

        $penjualanObj->insertDetailPenjualan($idpenjualan, $idbarang, $jumlah, $harga_jual);
        $penjualanObj->updateTotalPenjualan($idpenjualan);

        $_SESSION['success_message'] = " Detail barang berhasil ditambahkan!";
        header("Location: detail.php?id=" . $idpenjualan);
        exit();
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'> " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Hapus Detail Barang
if (isset($_GET['delete_detail'])) {
    try {
        $iddetail = (int)$_GET['delete_detail'];
        $penjualanObj->deleteDetailPenjualan($iddetail);
        $penjualanObj->updateTotalPenjualan($idpenjualan);
        
        $_SESSION['success_message'] = "Detail barang berhasil dihapus!";
        header("Location: detail.php?id=" . $idpenjualan);
        exit();
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'> " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// Selesai Transaksi
if (isset($_POST['selesai'])) {
    unset($_SESSION['penjualan_baru']);
    $_SESSION['success_message'] = " Transaksi penjualan berhasil diselesaikan!";
    header("Location: list.php");
    exit();
}

// Ambil semua barang yang tersedia
$barangList = $penjualanObj->getBarangTersedia();

// Ambil detail penjualan yang sudah ditambahkan
$detailList = $penjualanObj->getDetailPenjualan($idpenjualan);

// Hitung total
$totalSubtotal = $penjualan['subtotal'] ?? 0;
$ppn = $penjualan['ppn'] ?? 0;
$totalAkhir = $penjualan['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Penjualan #<?= htmlspecialchars($idpenjualan) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/table.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="container py-4" style="margin-left:260px;">
  <div class="card p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="d-flex align-items-center">
        <img src="https://cdn-icons-png.flaticon.com/512/2331/2331943.png" width="40" class="me-3">
        <div>
          <h4 class="fw-bold mb-0">📦 Detail Penjualan #<?= htmlspecialchars($idpenjualan) ?></h4>
          <small class="text-muted">
            Margin: <?= number_format($penjualan['margin_persen'] ?? 0, 2) ?>% | 
            Kasir: <?= htmlspecialchars($penjualan['kasir'] ?? $penjualan['petugas'] ?? 'N/A') ?> |
            Tanggal: <?= htmlspecialchars($penjualan['tanggal'] ?? 'N/A') ?>
          </small>
        </div>
      </div>
      <form method="POST" style="margin: 0;">
        <button type="submit" name="selesai" class="btn btn-success">
           Selesai & Kembali ke List
        </button>
      </form>
    </div>

    <?= $message ?>

    <!-- Form Tambah Barang -->
    <div class="card bg-light p-3 mb-4">
      <h5 class="fw-bold mb-3"> Tambah Barang</h5>
      
      <form method="POST">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label fw-bold">Pilih Barang</label>
            <select name="idbarang" id="idbarang" class="form-select" required onchange="updateHargaStok()">
              <option value="">-- Pilih Barang --</option>
              <?php foreach($barangList as $b): ?>
                <option 
                  value="<?= htmlspecialchars($b['idbarang']) ?>"
                  data-harga="<?= htmlspecialchars($b['harga_beli']) ?>"
                  data-stok="<?= htmlspecialchars($b['stock']) ?>"
                  data-satuan="<?= htmlspecialchars($b['nama_satuan']) ?>">
                  <?= htmlspecialchars($b['nama_barang']) ?> 
                  (Stok: <?= $b['stock'] ?> <?= htmlspecialchars($b['nama_satuan']) ?>) - 
                  Rp <?= number_format($b['harga_beli'], 0, ',', '.') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label fw-bold">Stok Tersedia</label>
            <input type="text" id="stok_tersedia" class="form-control" readonly placeholder="-">
          </div>

          <div class="col-md-2">
            <label class="form-label fw-bold">Jumlah</label>
            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" required onchange="hitungTotal()">
          </div>

          <div class="col-md-2">
            <label class="form-label fw-bold">Harga Jual</label>
            <input type="number" name="harga_jual" id="harga_jual" class="form-control" min="1" required onchange="hitungTotal()">
          </div>

          <div class="col-md-2">
            <label class="form-label fw-bold">Subtotal</label>
            <input type="text" id="subtotal_preview" class="form-control" readonly placeholder="0">
          </div>

          <div class="col-md-12">
            <button type="submit" name="submit_detail" class="btn btn-primary w-100">
              ➕ Tambah Barang ke Penjualan
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Tabel Detail Barang yang Sudah Ditambahkan -->
    <h5 class="fw-bold mb-3">📋 Barang yang Dijual</h5>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Jumlah</th>
            <th>Harga Jual</th>
            <th>Subtotal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($detailList)): ?>
            <tr><td colspan="7" class="text-center text-muted">Belum ada barang ditambahkan</td></tr>
          <?php else: ?>
            <?php $no = 1; foreach ($detailList as $detail): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($detail['nama_barang']) ?></td>
              <td><?= htmlspecialchars($detail['nama_satuan'] ?? 'N/A') ?></td>
              <td><?= $detail['jumlah_jual'] ?? $detail['jumlah'] ?? 0 ?></td>
              <td>Rp <?= number_format($detail['harga_jual'], 0, ',', '.') ?></td>
              <td>Rp <?= number_format($detail['sub_total_jual'] ?? $detail['subtotal'] ?? 0, 0, ',', '.') ?></td>
              <td>
                <a href="?id=<?= htmlspecialchars($idpenjualan) ?>&delete_detail=<?= $detail['iddetail_penjualan'] ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Yakin hapus barang ini?')">
                  🗑️ Hapus
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
        <tfoot class="table-light">
          <tr>
            <th colspan="5" class="text-end">Subtotal:</th>
            <th colspan="2">Rp <?= number_format($totalSubtotal, 0, ',', '.') ?></th>
          </tr>
          <tr>
            <th colspan="5" class="text-end">PPN (10%):</th>
            <th colspan="2">Rp <?= number_format($ppn, 0, ',', '.') ?></th>
          </tr>
          <tr class="table-success">
            <th colspan="5" class="text-end">TOTAL AKHIR:</th>
            <th colspan="2">Rp <?= number_format($totalAkhir, 0, ',', '.') ?></th>
          </tr>
        </tfoot>
      </table>
    </div>

  </div>
</div>

<script>
function updateHargaStok() {
  const select = document.getElementById('idbarang');
  const option = select.options[select.selectedIndex];
  
  const harga = option.getAttribute('data-harga') || '';
  const stok = option.getAttribute('data-stok') || '';
  const satuan = option.getAttribute('data-satuan') || '';
  
  // Update harga jual dengan margin
  const margin = <?= $penjualan['margin_persen'] ?? 10 ?>;
  const hargaJual = harga ? Math.ceil(parseFloat(harga) * (1 + margin / 100)) : '';
  
  document.getElementById('harga_jual').value = hargaJual;
  document.getElementById('stok_tersedia').value = stok ? stok + ' ' + satuan : '-';
  document.getElementById('jumlah').max = stok;
  
  hitungTotal();
}

function hitungTotal() {
  const jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
  const harga = parseFloat(document.getElementById('harga_jual').value) || 0;
  const subtotal = jumlah * harga;
  
  document.getElementById('subtotal_preview').value = 'Rp ' + subtotal.toLocaleString('id-ID');
}
</script>

</body>
</html>