<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../classes/MarginPenjualan.php");
$marginObj = new MarginPenjualan();

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$id = $_GET['id'];
$data = null;

// Ambil data margin dari view
$all = $marginObj->getAll("all");
foreach ($all as $m) {
    if ($m['kode_margin'] === $id) {
        $data = $m;
        break;
    }
}

if (!$data) die("Margin tidak ditemukan.");

$message = "";

// Proses Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $persen = $_POST['persen'];
    $iduser = $_SESSION['user']['iduser'];

    if ($marginObj->update($id, $persen, $iduser)) {
        header("Location: list.php?updated=1");
        exit();
    } else {
        $message = "Gagal mengupdate margin.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Margin Penjualan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/style/dashboard.css">

    <style>
        body {
            background: #f5f8ff;
            font-family: 'Inter', sans-serif;
        }

        .main {
            margin-left: 260px;
            padding: 30px;
        }

        .edit-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 35px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 8px 22px rgba(0,0,0,0.08);
            animation: fadeIn 0.4s ease;
        }

        .edit-card h2 {
            font-weight: 700;
            color: #222;
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #444;
        }

        .form-control {
            height: 48px;
            border-radius: 10px;
            border: 1px solid #dce3f0;
        }

        .form-control:focus {
            border-color: #4a8bff;
            box-shadow: 0 0 0 0.15rem rgba(74, 139, 255, 0.25);
        }

        .btn-primary {
            height: 48px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3477f5, #275df1);
            border: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(52,119,245,0.35);
        }

        .btn-secondary {
            height: 48px;
            border-radius: 10px;
            font-weight: 600;
            background: #6c757d;
            border: none;
            margin-top: 10px;
        }

        /* Animation */
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(12px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
    <div class="edit-card">

        <h2>✏️ Edit Margin Penjualan</h2>

        <?php if ($message): ?>
            <div class="alert alert-danger"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">ID Margin</label>
                <input type="text" class="form-control" value="<?= $data['kode_margin'] ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Persentase Margin (%)</label>
                <input type="number" step="0.1" name="persen" class="form-control"
                       value="<?= $data['persen_margin'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <input type="text" class="form-control" value="<?= $data['status_margin'] ?>" disabled>
            </div>

            <button class="btn btn-primary w-100">Update</button>
            <a href="list.php" class="btn btn-secondary w-100">Kembali</a>

        </form>
    </div>
</div>

</body>
</html>
