<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../classes/Penjualan.php");
$penjualanObj = new Penjualan();

$idpenjualan = $_GET['id'] ?? null;
if (!$idpenjualan) { header("Location: list.php"); exit(); }

// Data penjualan
$penjualan = $penjualanObj->getById($idpenjualan);
if (!$penjualan) {
    $_SESSION['error_message'] = "Penjualan tidak ditemukan!";
    header("Location: list.php");
    exit();
}

$barangList   = $penjualanObj->getBarangTersedia();
$detailList   = $penjualanObj->getDetailPenjualan($idpenjualan);
$totalSubtotal = $penjualan['subtotal'] ?? 0;
$ppn          = $penjualan['ppn'] ?? 0;
$totalAkhir   = $penjualan['total'] ?? 0;

// Tambah Detail
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_detail'])) {
    try {
        $penjualanObj->insertDetailPenjualan(
            $idpenjualan,
            $_POST['idbarang'],
            $_POST['jumlah'],
            $_POST['harga_jual']
        );
        $penjualanObj->updateTotalPenjualan($idpenjualan);

        $_SESSION['success_message'] = "Barang berhasil ditambahkan!";
        header("Location: detail.php?id=" . $idpenjualan);
        exit();
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>{$e->getMessage()}</div>";
    }
}

// Hapus detail
if (isset($_GET['delete_detail'])) {
    try {
        $penjualanObj->deleteDetailPenjualan($_GET['delete_detail']);
        $penjualanObj->updateTotalPenjualan($idpenjualan);
        $_SESSION['success_message'] = "Detail berhasil dihapus!";
        header("Location: detail.php?id=" . $idpenjualan);
        exit();
    } catch (Exception $e) { }
}

// Selesai
if (isset($_POST['selesai'])) {
    unset($_SESSION['penjualan_baru']);
    $_SESSION['success_message'] = "Penjualan berhasil diselesaikan!";
    header("Location: list.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Penjualan #<?= $idpenjualan ?></title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="../../assets/style/dashboard.css">
    <link rel="stylesheet" href="../../assets/style/list-table.css">

    <!-- Icons + Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .header-box {
            background: white;
            padding: 25px;
            border-radius: 18px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .section-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        .btn-save {
            background: #2e7d32;
            color: white;
            font-weight: 600;
            border-radius: 12px;
            padding: 10px 18px;
        }

        .btn-save:hover {
            background: #1b5e20;
            color: white;
        }

        .btn-add-barang {
            background: #1565c0;
            color: white;
            padding: 10px;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
        }

        .btn-add-barang:hover {
            background: #0d47a1;
            color: white;
        }

        .btn-delete-custom {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .btn-delete-custom:hover {
            background: #e53935;
            color: white;
            border-color: #e53935;
        }

        .table thead {
            background: #2e7d32 !important;
            color: white;
        }
    </style>
</head>

<body>
    <?php include(__DIR__ . '/../layout/sidebar.php'); ?>

    <div class="main">
        <div class="container-fluid">

            <!-- HEADER -->
            <div class="header-box d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold">
                        <i class="bi bi-receipt-cutoff text-success"></i>
                        Detail Penjualan <span class="text-success">#<?= $idpenjualan ?></span>
                    </h3>
                    <small class="text-muted">
                        Margin: <?= number_format($penjualan['margin_persen'], 2) ?>% |
                        Kasir: <?= $penjualan['kasir'] ?> |
                        Tanggal: <?= $penjualan['tanggal'] ?>
                    </small>
                </div>

                <form method="POST">
                    <button class="btn btn-save" name="selesai">
                        <i class="bi bi-check-circle"></i> Selesai & Kembali
                    </button>
                </form>
            </div>

            <!-- FORM TAMBAH BARANG -->
            <div class="section-card mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-box-seam"></i> Tambah Barang</h5>

                <form method="POST">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="fw-semibold">Pilih Barang</label>
                            <select name="idbarang" id="idbarang" class="form-select" onchange="updateHargaStok()" required>
                                <option value="">-- Pilih Barang --</option>
                                <?php foreach ($barangList as $b): ?>
                                    <option value="<?= $b['idbarang'] ?>"
                                        data-harga="<?= $b['harga_beli'] ?>"
                                        data-stok="<?= $b['stock'] ?>"
                                        data-satuan="<?= $b['nama_satuan'] ?>">
                                        <?= $b['nama_barang'] ?> (Stok: <?= $b['stock'] ?> <?= $b['nama_satuan'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="fw-semibold">Stok</label>
                            <input id="stok_tersedia" class="form-control" readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="fw-semibold">Jumlah</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" onchange="hitungTotal()" required>
                        </div>

                        <div class="col-md-2">
                            <label class="fw-semibold">Harga Jual</label>
                            <input type="number" name="harga_jual" id="harga_jual" class="form-control" onchange="hitungTotal()" required>
                        </div>

                        <div class="col-md-2">
                            <label class="fw-semibold">Subtotal</label>
                            <input id="subtotal_preview" class="form-control" readonly>
                        </div>

                        <div class="col-12">
                            <button name="submit_detail" class="btn-add-barang">
                                <i class="bi bi-plus-circle"></i> Tambah Barang ke Penjualan
                            </button>
                        </div>

                    </div>
                </form>
            </div>

            <!-- DETAIL TABLE -->
            <div class="section-card mb-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-boxes"></i> Barang yang Dijual
                </h5>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
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
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">
                                        <i class="bi bi-inbox"></i> Belum ada barang
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($detailList as $d): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $d['nama_barang'] ?></td>
                                        <td><?= $d['nama_satuan'] ?></td>
                                        <td><?= $d['jumlah_jual'] ?></td>
                                        <td>Rp <?= number_format($d['harga_jual']) ?></td>
                                        <td>Rp <?= number_format($d['subtotal']) ?></td>
                                        <td>
                                            <a href="?id=<?= $idpenjualan ?>&delete_detail=<?= $d['iddetail_penjualan'] ?>"
                                               class="btn btn-delete-custom btn-sm"
                                               onclick="return confirm('Hapus barang ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>

                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Subtotal:</th>
                                <th colspan="2">Rp <?= number_format($totalSubtotal) ?></th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end">PPN:</th>
                                <th colspan="2">Rp <?= number_format($ppn) ?></th>
                            </tr>
                            <tr class="table-success fw-bold">
                                <th colspan="5" class="text-end">TOTAL AKHIR:</th>
                                <th colspan="2">Rp <?= number_format($totalAkhir) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        function updateHargaStok() {
            let option = document.querySelector("#idbarang option:checked");
            let harga = option.dataset.harga || 0;
            let stok = option.dataset.stok || 0;
            let satuan = option.dataset.satuan || "";

            let margin = <?= $penjualan['margin_persen'] ?>;
            let hargaJual = Math.ceil(harga * (1 + margin / 100));

            document.getElementById('stok_tersedia').value = stok + " " + satuan;
            document.getElementById('harga_jual').value = hargaJual;
            document.getElementById('jumlah').max = stok;

            hitungTotal();
        }

        function hitungTotal() {
            let jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
            let harga = parseFloat(document.getElementById('harga_jual').value) || 0;

            let subtotal = jumlah * harga;
            document.getElementById('subtotal_preview').value = "Rp " + subtotal.toLocaleString("id-ID");
        }
    </script>

</body>
</html>
