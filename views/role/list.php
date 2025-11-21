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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Role | Sistem Farmasi</title>

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- font poppins -->
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Global Dashboard Style -->
  <link rel="stylesheet" href="../../assets/style/dashboard.css">

  <!-- Tabel + Search Style -->
  <link rel="stylesheet" href="../../assets/style/list-table.css">
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main">
  <div class="container-fluid">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
      <h1 class="page-title">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" width="42">
        Data Role
      </h1>

      <!-- Search Box -->
      <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" id="searchInput" class="form-control" placeholder="Cari ID atau nama role...">
      </div>
    </div>

    <!-- Tombol Tambah -->
    <div class="mb-4">
      <a href="add.php" class="btn-add">
        <i class="bi bi-plus-circle-fill"></i> Tambah Role
      </a>
    </div>

    <!-- Tabel -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i> Daftar Role</h5>
        <span class="badge bg-light text-dark fs-6"><?= count($data) ?> item</span>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">

          <table class="table table-hover align-middle mb-0" id="roleTable">
            <thead>
              <tr>
                <th>ID Role</th>
                <th>Nama Role</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>

            <tbody>
              <?php if (!empty($data)): ?>
                <?php foreach ($data as $row): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['kode_role']) ?></td>
                    <td><?= htmlspecialchars($row['nama_role']) ?></td>

                    <td class="text-center">
                      <a href="edit.php?id=<?= $row['kode_role'] ?>" class="action-btn btn-edit" title="Edit">
                        <i class="bi bi-pencil-fill"></i>
                      </a>

                      <a href="delete.php?id=<?= $row['kode_role'] ?>" class="action-btn btn-delete ms-2"
                         onclick="return confirm('Yakin ingin menghapus role ini?')">
                        <i class="bi bi-trash-fill"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="3" class="no-data">
                    <i class="bi bi-inbox"></i>
                    <div>Belum ada data role.</div>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>

          </table>

        </div>
      </div>
    </div>

  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Table Search -->
<script src="../../assets/js/table-search.js"></script>

</body>
</html>
