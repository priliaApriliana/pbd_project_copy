<!-- File ini akan generate form retur per barang. -->

<?php
require_once(__DIR__ . '/../../classes/ReturBarang.php');

$id = $_GET['id'];
$retur = new ReturBarang();
$data = $retur->getDetailPenerimaan($id);

?>

<table class="table table-bordered mt-3">
    <thead>
        <tr class="table-primary">
            <th>ID Detail</th>
            <th>Barang</th>
            <th>Jumlah Diterima</th>
            <th>Retur</th>
            <th>Alasan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
        <tr>
            <td><?= $row['iddetail_penerimaan'] ?></td>
            <td><?= $row['nama_barang'] ?></td>
            <td><?= $row['jumlah_terima'] ?></td>

            <td>
                <input type="number" name="retur_jumlah[<?= $row['iddetail_penerimaan'] ?>]"
                       max="<?= $row['jumlah_terima'] ?>" min="0" class="form-control">
            </td>

            <td>
                <input type="text" name="retur_alasan[<?= $row['iddetail_penerimaan'] ?>]"
                       class="form-control" placeholder="Alasan retur">
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
