<?php
session_start();
if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

require_once(__DIR__ . "/../../classes/User.php");
$userObj = new User();
$data = $userObj->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/style/dashboard.css">
  <link rel="stylesheet" href="../../assets/style/table.css">
</head>
<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
  <div class="container">
    <div class="page-header d-flex align-items-center mb-3">
      <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" width="36" style="margin-right:8px;">
      <h2>Data User</h2>
    </div>

    <a href="add.php" class="btn-add w-100 mb-3">+ Tambah User</a>

    <div class="table-card">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID User</th>
            <th>Username</th>
            <th>Role</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data as $row): ?>
            <tr>
            <td><?= htmlspecialchars($row['kode_user'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['username'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['role'] ?? '-') ?></td>

              <td>
                <a href="edit.php?id=<?= $row['kode_user'] ?>" class="btn-edit">Edit</a>
                <a href="delete.php?id=<?= $row['kode_user'] ?>" class="btn-delete" onclick="return confirm('Hapus user ini?')">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($data)): ?>
            <tr><td colspan="4" class="no-data">Belum ada data user.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
