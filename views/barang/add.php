<?php
// views/barang/add.php
require_once(__DIR__ . "/../../classes/Barang.php");

$barangObj = new Barang();
$error = "";

// AUTO GENERATE ID BARANG
function generateNextId($barangObj): string
{
    $last = $barangObj->getLastIdBarang();
    if (!$last) return "B001";
    preg_match('/B(\d+)/', $last, $matches);
    $number = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
    return 'B' . str_pad($number, 3, '0', STR_PAD_LEFT);
}

$nextId = generateNextId($barangObj);
$satuanList = $barangObj->getSatuanOptions();

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis    = trim($_POST['jenis']);
    $nama     = trim($_POST['nama']);
    $idsatuan = trim($_POST['idsatuan']);
    $status   = trim($_POST['status']);
    $harga    = trim($_POST['harga']);

    if ($jenis === "" || $nama === "" || $idsatuan === "" || $harga === "") {
        $error = "Semua field wajib diisi!";
    } else {
        if ($barangObj->create($nextId, $jenis, $nama, $idsatuan, (int)$status, (int)$harga)) {
            header("Location: list.php?msg=success");
            exit();
        } else {
            $error = "Gagal menambah barang.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>

    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/style/dashboard.css">

    <style>
        /* POPPINS UNTUK SELURUH HALAMAN */
        body, input, select, button, textarea {
            font-family: 'Poppins', sans-serif !important;
        }

        :root {
            --green: #28a745;
            --green-dark: #1e7e34;
        }

        body { 
            background-color: #f8f9fa; 
            color: #333;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }

        .page-header {
            background: var(--green);
            color: white;
            padding: 1.8rem 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 25px rgba(40,167,69,0.3);
            text-align: center;
        }

        .page-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-body {
            padding: 3rem 2.5rem;
            background: white;
        }

        .form-label {
            font-weight: 600;
            color: #2d3436;
            font-size: 1.05rem;
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #ddd;
            padding: 0.9rem 1.2rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 0.25rem rgba(40,167,69,0.2);
            background-color: #f8fff9;
        }

        .readonly-input {
            background: linear-gradient(135deg, #e8f7e9, #f0fdf4) !important;
            border: 3px dashed var(--green) !important;
            color: var(--green-dark) !important;
            font-weight: 700;
            font-size: 1.8rem;
            text-align: center;
            letter-spacing: 6px;
            text-transform: uppercase;
        }

        .btn-success {
            background: var(--green);
            border: none;
            border-radius: 50px;
            padding: 0.9rem 3rem;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 8px 20px rgba(40,167,69,0.4);
            transition: all 0.3s ease;
        }

        .btn-success:hover {
             background: var(--green-dark);
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(40,167,69,0.5);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 50px;
            padding: 0.9rem 3rem;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        small.text-success {
            font-weight: 500;
            color: var(--green) !important;
        }

        @media (max-width: 768px) {
            .main-content { 
                margin-left: 0; 
                padding: 1rem; 
            }
            .page-header { 
                padding: 1.5rem; 
                border-radius: 12px; 
            }
            .card-body { 
                padding: 2rem 1.5rem; 
            }
            .readonly-input {
                font-size: 1.4rem;
                letter-spacing: 3px;
            }
        }
    </style>
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main-content">

    <!-- Header: Data Barang -->
    <div class="page-header">
        <h1>Data Barang</h1>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                <?php if ($error): ?>
                    <div class="alert alert-danger mb-4 rounded-3">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <!-- ID BARANG AUTO -->
                    <div class="mb-4">
                        <label class="form-label">ID Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control readonly-input" 
                               value="<?= htmlspecialchars($nextId) ?>" readonly>
                        <small class="text-success">ID otomatis – tidak dapat diubah</small>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Jenis <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-select" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="F">Food & Beverage</option>
                                <option value="H">Health & Beauty</option>
                                <option value="S">Stationary</option>
                                <option value="C">Cleaning & Household</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Satuan <span class="text-danger">*</span></label>
                            <select name="idsatuan" class="form-select" required>
                                <option value="">-- Pilih Satuan --</option>
                                <?php foreach ($satuanList as $s): ?>
                                    <option value="<?= $s['kode_satuan'] ?>">
                                        <?= $s['kode_satuan'] ?> - <?= $s['nama_satuan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4 mt-4">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama barang" required>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                            <input type="number" name="harga" class="form-control" min="0" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-success">
                            Simpan Data
                        </button>
                        <a href="list.php" class="btn btn-secondary ms-3">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>