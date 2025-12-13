<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 🔥 PBD: Gunakan Class untuk ambil data dari database
require_once(__DIR__ . '/../classes/Pengadaan.php');
require_once(__DIR__ . '/../classes/Penerimaan.php');
require_once(__DIR__ . '/../classes/Penjualan.php');

// Ambil data real dari database
$pengadaanObj = new Pengadaan();
$penerimaanObj = new Penerimaan();
$penjualanObj = new Penjualan();

// Hitung total per kategori
$totalPengadaan = count($pengadaanObj->getAll('all'));
$totalPenerimaan = count($penerimaanObj->getAllPenerimaan('all'));
$totalPenjualan = count($penjualanObj->getAll());

// Hitung total nilai (bisa pakai method khusus atau query VIEW)
$pengadaanData = $pengadaanObj->getAll();
$totalNilaiPengadaan = array_sum(array_column($pengadaanData, 'total_nilai'));

$penjualanData = $penjualanObj->getAll();
$totalNilaiPenjualan = array_sum(array_column($penjualanData, 'total'));

// Data dummy untuk user, vendor, barang (atau buat class tersendiri)
$totalUsers = 3;
$totalVendors = 6;
$totalBarang = 15;

$username = $_SESSION['user']['username'] ?? 'Admin';
$role = $_SESSION['user']['role'] ?? 'Super Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Sistem Farmasi</title>

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/style/dashboard.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f9f0 0%, #e8f5e8 100%);
            min-height: 100vh;
        }
        
        .main {
            margin-left: 280px;
            padding: 30px 20px;
            transition: all 0.3s;
        }
        
        /* Header Section */
        .dashboard-header {
            margin-bottom: 30px;
        }
        
        .page-title {
            color: #1e6b2a;
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 8px;
        }
        
        .page-subtitle {
            color: #666;
            font-size: 16px;
            font-weight: 400;
        }
        
        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #4caf50;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(76,175,80,0.1), transparent);
            border-radius: 0 0 0 100%;
        }
        
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 16px;
        }
        
        .stat-card.users .stat-icon {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            color: #1976d2;
        }
        
        .stat-card.vendors .stat-icon {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            color: #f57c00;
        }
        
        .stat-card.products .stat-icon {
            background: linear-gradient(135deg, #f3e5f5, #e1bee7);
            color: #7b1fa2;
        }
        
        .stat-card.pengadaan .stat-icon {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            color: #2e7d32;
        }
        
        .stat-card.penerimaan .stat-icon {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            color: #1565c0;
        }
        
        .stat-card.penjualan .stat-icon {
            background: linear-gradient(135deg, #fff3e0, #ffcc80);
            color: #ef6c00;
        }
        
        .stat-label {
            color: #666;
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .stat-value {
            color: #2e7d32;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .stat-subtitle {
            color: #999;
            font-size: 12px;
            margin-top: 8px;
        }
        
        .stat-change {
            color: #4caf50;
            font-size: 13px;
            font-weight: 600;
        }
        
        .stat-change i {
            font-size: 12px;
        }
        
        /* Section Title */
        .section-title {
            color: #2e7d32;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Quick Actions */
        .quick-actions {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-top: 30px;
        }
        
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .action-btn {
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            background: white;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: #333;
        }
        
        .action-btn:hover {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-color: #4caf50;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(76,175,80,0.2);
        }
        
        .action-btn i {
            font-size: 28px;
            color: #4caf50;
            margin-bottom: 8px;
        }
        
        .action-btn span {
            display: block;
            font-size: 13px;
            font-weight: 500;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .main {
                margin-left: 0;
                padding: 20px 15px;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .action-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .page-title {
                font-size: 24px;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .stat-value {
                font-size: 24px;
            }
            
            .action-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<?php include(__DIR__ . '/layout/sidebar.php'); ?>

<div class="main">
    <div class="container-fluid">
        
        <!-- Header -->
        <div class="dashboard-header">
            <h1 class="page-title">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </h1>
            <p class="page-subtitle">Ringkasan data keseluruhan sistem farmasi</p>
        </div>
        
        <!-- Section: Data Master -->
        <h5 class="section-title">
            <i class="bi bi-database-fill"></i>
            Data Master
        </h5>
        
        <div class="stats-container">
            <!-- Total Users -->
            <div class="stat-card users" onclick="location.href='user/list.php'">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-label">User Terdaftar</div>
                <div class="stat-value"><?= number_format($totalUsers) ?></div>
                <div class="stat-subtitle">Klik untuk lihat detail</div>
            </div>
            
            <!-- Total Vendors -->
            <div class="stat-card vendors" onclick="location.href='vendor/list.php'">
                <div class="stat-icon">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-label">Vendor Aktif</div>
                <div class="stat-value"><?= number_format($totalVendors) ?></div>
                <div class="stat-subtitle">Klik untuk lihat detail</div>
            </div>
            
            <!-- Total Products -->
            <div class="stat-card products" onclick="location.href='barang/list.php'">
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-label">Total Barang</div>
                <div class="stat-value"><?= number_format($totalBarang) ?></div>
                <div class="stat-subtitle">Klik untuk lihat detail</div>
            </div>
        </div>
        
        <!-- Section: Transaksi -->
        <h5 class="section-title mt-4">
            <i class="bi bi-receipt-cutoff"></i>
            Rekapitulasi Transaksi
        </h5>
        
        <div class="stats-container">
            <!-- Total Pengadaan -->
            <div class="stat-card pengadaan" onclick="location.href='pengadaan/list.php'">
                <div class="stat-icon">
                    <i class="bi bi-cart-check-fill"></i>
                </div>
                <div class="stat-label">Total Pengadaan</div>
                <div class="stat-value"><?= number_format($totalPengadaan) ?></div>
                <div class="stat-subtitle">
                    Total Nilai: <strong>Rp <?= number_format($totalNilaiPengadaan / 1000000, 1) ?>Jt</strong>
                </div>
            </div>
            
            <!-- Total Penerimaan -->
            <div class="stat-card penerimaan" onclick="location.href='penerimaan/list.php'">
                <div class="stat-icon">
                    <i class="bi bi-house-fill"></i>
                </div>
                <div class="stat-label">Total Penerimaan</div>
                <div class="stat-value"><?= number_format($totalPenerimaan) ?></div>
                <div class="stat-subtitle">
                    Barang yang sudah diterima
                </div>
            </div>
            
            <!-- Total Penjualan -->
            <div class="stat-card penjualan" onclick="location.href='penjualan/list.php'">
                <div class="stat-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="stat-label">Total Penjualan</div>
                <div class="stat-value"><?= number_format($totalPenjualan) ?></div>
                <div class="stat-subtitle">
                    Total Pendapatan: <strong>Rp <?= number_format($totalNilaiPenjualan / 1000000, 1) ?>Jt</strong>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <h5 class="section-title">
                <i class="bi bi-lightning-fill"></i>
                Aksi Cepat
            </h5>
            <div class="action-grid">
                <a href="barang/add.php" class="action-btn">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Tambah Barang</span>
                </a>
                <a href="vendor/add.php" class="action-btn">
                    <i class="bi bi-building-add"></i>
                    <span>Tambah Vendor</span>
                </a>
                <a href="pengadaan/add.php" class="action-btn">
                    <i class="bi bi-cart-plus"></i>
                    <span>Buat Pengadaan</span>
                </a>
                <a href="penerimaan/add.php" class="action-btn">
                    <i class="bi bi-inbox"></i>
                    <span>Terima Barang</span>
                </a>
                <a href="penjualan/add.php" class="action-btn">
                    <i class="bi bi-cash-stack"></i>
                    <span>Transaksi Baru</span>
                </a>
                <a href="kartu_stok/list.php" class="action-btn">
                    <i class="bi bi-layers-fill"></i>
                    <span>Kartu Stok</span>
                </a>
            </div>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>