<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../classes/Role.php");
$roleObj = new Role();
$data = $roleObj->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Role</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/table.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
  <div class="container">
    <div class="d-flex justify-content-between align-items-start mb-3">
      <div class="page-header">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" width="36" style="margin-right:8px;">
        <h2>Data Role</h2>
      </div>
    </div>

    <a href="add.php" class="btn-add w-100 mb-3">+ Tambah Role</a>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID Role</th>
            <th>Nama Role</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['kode_role']) ?></td>
              <td><?= htmlspecialchars($row['nama_role']) ?></td>
              <td>
                <a href="edit.php?id=<?= $row['kode_role'] ?>" class="btn-edit">Edit</a>
                <a href="delete.php?id=<?= $row['kode_role'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus role ini?')">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($data)): ?>
            <tr><td colspan="3" class="no-data">Belum ada data role.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>