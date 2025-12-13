<?php
require_once(__DIR__ . '/BaseModel.php');

class KartuStok extends BaseModel
{
    public function getAllKartuStok()
    {
        $sql = "SELECT * FROM v_kartu_stok_detail ORDER BY tanggal DESC";
        return $this->query($sql);
    }
}
