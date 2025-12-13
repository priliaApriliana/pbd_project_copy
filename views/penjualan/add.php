<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../classes/Penjualan.php");
$penjualanObj = new Penjualan();

$message = '';

// Ambil daftar margin aktif untuk dropdown
$marginList = $penjualanObj->getMarginAktif();

// Simpan Data Penjualan (Header)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_penjualan'])) {
    try {
        $iduser = $_SESSION['user']['iduser'] ?? 'U002';
        $idmargin_penjualan = $_POST['idmargin_penjualan'];
        
        // Insert header penjualan
        $idpenjualan = $penjualanObj->insertPenjualan($iduser, $idmargin_penjualan);
        
        // Redirect ke halaman detail
        $_SESSION['success_message'] = "Header penjualan berhasil dibuat. Silakan tambahkan detail barang.";
        header("Location: detail.php?id=" . $idpenjualan);
        exit();
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'><i class='bi bi-exclamation-triangle-fill me-2'></i>" . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Penjualan Baru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/table.css">
  
  <!-- Fonts + Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/icons/bootstrap-icons.min.css">
  
  <style>
    body {
      background-color: #f5f5f5;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
    }
    
    .main-container {
      margin-left: 260px;
      padding: 2rem;
    }
    
    .page-header {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 2rem;
    }
    
    .page-header h4 {
      color: #2d5016;
      font-weight: 700;
      margin: 0;
      font-size: 1.75rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .page-icon {
      font-size: 2.5rem;
    }
    
    .add-button-container {
      background: linear-gradient(135deg, #4a7c2c 0%, #5d9938 100%);
      border-radius: 12px;
      padding: 1.25rem;
      margin-bottom: 2rem;
      box-shadow: 0 4px 12px rgba(74, 124, 44, 0.2);
    }
    
    .add-button-container button {
      background: white;
      color: #4a7c2c;
      border: none;
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      width: 100%;
      transition: all 0.3s ease;
    }
    
    .add-button-container button:hover {
      background: #f0f0f0;
      transform: translateY(-2px);
    }
    
    .form-card {
      background: white;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .form-header {
      background: linear-gradient(135deg, #4a7c2c 0%, #5d9938 100%);
      color: white;
      padding: 1rem 1.5rem;
      border-radius: 10px;
      margin-bottom: 2rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-weight: 600;
      font-size: 1.1rem;
    }
    
    .form-label {
      color: #2d3748;
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 0.5rem;
      display: block;
    }
    
    .form-control, .form-select {
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
      border-color: #4a7c2c;
      box-shadow: 0 0 0 3px rgba(74, 124, 44, 0.1);
      outline: none;
    }
    
    .form-control:disabled, .form-control[readonly] {
      background-color: #f7fafc;
      color: #718096;
      cursor: not-allowed;
    }
    
    .form-text {
      color: #718096;
      font-size: 0.85rem;
      margin-top: 0.25rem;
      display: block;
    }
    
    .info-box {
      background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
      border-left: 4px solid #4a7c2c;
      border-radius: 8px;
      padding: 1.25rem;
      margin: 1.5rem 0;
    }
    
    .info-box strong {
      color: #2d5016;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.5rem;
      font-size: 1rem;
    }
    
    .info-box p {
      color: #4a7c2c;
      margin: 0;
      font-size: 0.9rem;
      line-height: 1.5;
    }
    
    .button-group {
      display: flex;
      gap: 1rem;
      margin-top: 2rem;
      padding-top: 2rem;
      border-top: 2px solid #e2e8f0;
    }
    
    .btn-success-custom {
      background: linear-gradient(135deg, #4a7c2c 0%, #5d9938 100%);
      border: none;
      border-radius: 8px;
      padding: 0.875rem 2rem;
      font-weight: 600;
      color: white;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(74, 124, 44, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }
    
    .btn-success-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(74, 124, 44, 0.4);
      background: linear-gradient(135deg, #5d9938 0%, #4a7c2c 100%);
      color: white;
    }
    
    .btn-secondary-custom {
      background: white;
      border: 2px solid #cbd5e0;
      border-radius: 8px;
      padding: 0.875rem 2rem;
      font-weight: 600;
      color: #4a5568;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      text-decoration: none;
    }
    
    .btn-secondary-custom:hover {
      background: #f7fafc;
      border-color: #a0aec0;
      color: #2d3748;
      text-decoration: none;
    }
    
    .alert {
      border: none;
      border-radius: 8px;
      padding: 1rem 1.25rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
    }
    
    .alert-danger {
      background: #fff5f5;
      color: #c53030;
      border-left: 4px solid #fc8181;
    }
    
    .item-count {
      background: white;
      color: #4a7c2c;
      padding: 0.375rem 1rem;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.9rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    
    @media (max-width: 768px) {
      .main-container {
        margin-left: 0;
        padding: 1rem;
      }
      
      .button-group {
        flex-direction: column;
      }
      
      .btn-success-custom, .btn-secondary-custom {
        width: 100%;
      }
      
      .page-header h4 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main-container">
  <!-- Page Header -->
  <div class="page-header">
    <span class="page-icon">🛒</span>
    <h4>Tambah Penjualan Baru</h4>
  </div>

  <?= $message ?>

  <!-- Form Card -->
  <div class="form-card">
    <div class="form-header">
      <i class="bi bi-receipt"></i>
      <span>Form Penjualan Baru</span>
    </div>

    <form method="POST">
      <div class="row g-4">
        <div class="col-md-4">
          <label class="form-label">Petugas (Username)</label>
          <input type="text" class="form-control" 
                value="<?= htmlspecialchars($_SESSION['user']['username'] ?? 'unknown') ?>" readonly>
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Pilih Margin Penjualan</label>
          <select name="idmargin_penjualan" class="form-select" required>
            <option value="">-- Pilih Margin --</option>
            <?php foreach($marginList as $margin): ?>
              <option value="<?= htmlspecialchars($margin['idmargin_penjualan']) ?>">
                <?= htmlspecialchars($margin['display_text']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <small class="form-text">Margin menentukan harga jual barang</small>
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Tanggal</label>
          <input type="text" class="form-control" 
                value="<?= date('d/m/Y H:i:s') ?>" readonly>
        </div>
      </div>

      <div class="info-box">
        <strong>
          <i class="bi bi-info-circle-fill"></i>
          Info
        </strong>
        <p>Setelah membuat header penjualan, Anda akan diarahkan ke halaman detail untuk menambahkan barang yang dijual.</p>
      </div>

      <div class="button-group">
        <button type="submit" name="submit_penjualan" class="btn-success-custom">
          <i class="bi bi-check-circle-fill"></i>
          Buat Penjualan & Lanjut ke Detail
        </button>
        <a href="list.php" class="btn-secondary-custom">
          <i class="bi bi-arrow-left"></i>
          Kembali ke List
        </a>
      </div>
    </form>
  </div>
</div>

</body>
</html>