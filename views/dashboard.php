<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Simulasi data (ganti dengan query database Anda)
$totalUsers = 3;
$totalVendors = 6;
$totalBarang = 15;
$totalRevenue = 325892000; // Rp 325.892.000

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
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
        
        .stat-card.revenue .stat-icon {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            color: #2e7d32;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .stat-value {
            color: #2e7d32;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .stat-change {
            color: #4caf50;
            font-size: 13px;
            font-weight: 600;
        }
        
        .stat-change.negative {
            color: #f44336;
        }
        
        .stat-change i {
            font-size: 12px;
        }
        
        /* Chart Section */
        .chart-section {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .chart-title {
            color: #2e7d32;
            font-size: 20px;
            font-weight: 600;
        }
        
        .chart-filter {
            display: flex;
            gap: 10px;
        }
        
        .filter-btn {
            padding: 8px 16px;
            border: 1px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            font-size: 13px;
            color: #666;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: #4caf50;
            color: white;
            border-color: #4caf50;
        }
        
        /* Quick Actions */
        .quick-actions {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .quick-actions h5 {
            color: #2e7d32;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
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
                grid-template-columns: 1fr;
            }
            
            .chart-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .action-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .page-title {
                font-size: 24px;
            }
            
            .stat-value {
                font-size: 28px;
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
        
        <!-- Stats Cards -->
        <div class="stats-container">
            <!-- Total Users -->
            <div class="stat-card users">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-label">User Terdaftar</div>
                <div class="stat-value"><?= number_format($totalUsers) ?></div>
                <div class="stat-change">
                    <i class="bi bi-arrow-up-short"></i> +12% dari bulan lalu
                </div>
            </div>
            
            <!-- Total Vendors -->
            <div class="stat-card vendors">
                <div class="stat-icon">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-label">Vendor Aktif</div>
                <div class="stat-value"><?= number_format($totalVendors) ?></div>
                <div class="stat-change">
                    <i class="bi bi-arrow-up-short"></i> +8% dari bulan lalu
                </div>
            </div>
            
            <!-- Total Products -->
            <div class="stat-card products">
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-label">Total Barang</div>
                <div class="stat-value"><?= number_format($totalBarang) ?></div>
                <div class="stat-change">
                    <i class="bi bi-arrow-up-short"></i> +5% dari bulan lalu
                </div>
            </div>
            
            <!-- Total Revenue -->
            <div class="stat-card revenue">
                <div class="stat-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value">Rp <?= number_format($totalRevenue / 1000000, 1) ?>Jt</div>
                <div class="stat-change">
                    <i class="bi bi-arrow-up-short"></i> +18% dari bulan lalu
                </div>
            </div>
        </div>
        
        <!-- Chart Section -->
        <div class="chart-section">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="bi bi-graph-up me-2"></i>Statistik Penjualan
                </h3>
                <div class="chart-filter">
                    <button class="filter-btn active">7 Hari</button>
                    <button class="filter-btn">30 Hari</button>
                    <button class="filter-btn">12 Bulan</button>
                </div>
            </div>
            <div style="text-align: center; padding: 60px 20px; color: #999;">
                <i class="bi bi-bar-chart" style="font-size: 48px; color: #c8e6c9; margin-bottom: 15px;"></i>
                <p>Grafik penjualan akan ditampilkan di sini</p>
                <small>Integrasi dengan Chart.js atau library visualization lainnya</small>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <h5><i class="bi bi-lightning-fill me-2"></i>Aksi Cepat</h5>
            <div class="action-grid">
                <a href="views/barang/add.php" class="action-btn">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Tambah Barang</span>
                </a>
                <a href="views/vendor/add.php" class="action-btn">
                    <i class="bi bi-building-add"></i>
                    <span>Tambah Vendor</span>
                </a>
                <a href="views/pengadaan/add.php" class="action-btn">
                    <i class="bi bi-cart-plus"></i>
                    <span>Buat Pengadaan</span>
                </a>
                <a href="views/penjualan/add.php" class="action-btn">
                    <i class="bi bi-cash-stack"></i>
                    <span>Transaksi Baru</span>
                </a>
                <a href="views/user/list.php" class="action-btn">
                    <i class="bi bi-people"></i>
                    <span>Kelola User</span>
                </a>
                <a href="views/barang/list.php" class="action-btn">
                    <i class="bi bi-box-seam"></i>
                    <span>Lihat Stok</span>
                </a>
            </div>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Filter button functionality
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        console.log('Filter changed to:', this.textContent);
        // Tambahkan logic untuk update chart di sini
    });
});
</script>

</body>
</html> 