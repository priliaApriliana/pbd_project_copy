<?php
require_once(__DIR__ . '/../../config/DBConnection.php');
session_start();

if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

$db = new DBConnection();
$conn = $db->getConnection();

$username = $_SESSION['user']['username'] ?? 'Tidak diketahui';
$iduser   = $_SESSION['user']['iduser'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Pengadaan Barang</title>

<!-- Fonts + Icons -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
/* ==== GLOBAL ==== */
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f9fff9 0%, #ebf8f1 100%);
    margin: 0;
}

.main-content {
    margin-left: 280px;
    padding: 30px 30px;
}

/* ==== HEADER ==== */
.page-header {
    background: linear-gradient(135deg, #2e7d32, #4caf50);
    border-radius: 16px;
    padding: 22px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    font-weight: 600;
    font-size: 22px;
    max-width: 1100px;
    margin: auto;
}

.page-header a {
    color: white;
    text-decoration: none;
    font-size: 15px;
    background: rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 10px;
    transition: 0.3s;
}
.page-header a:hover{
    background: rgba(255,255,255,0.35);
}

/* ==== CARD ==== */
.card {
    background: white;
    padding: 28px;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    max-width: 1100px;
    margin: 20px auto 40px;
}

/* ==== INFO BOX ==== */
.info-box{
    background: #e3f2fd;
    border-left: 5px solid #2196f3;
    padding: 16px 22px;
    border-radius: 12px;
    margin-bottom: 25px;
    font-size: 15px;
}

/* ==== GRID FORM ==== */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

label {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 6px;
}

select, .form-control{
    width: 100%;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1.8px solid #c8e6c9;
    background: #fafffa;
    font-size: 14px;
}

select:focus, input:focus {
    outline: none;
    border-color: #4caf50;
    box-shadow: 0 0 0 3px rgba(76,175,80,0.2);
}

/* ==== ADD SECTION ==== */
.add-section {
    background: #f1fdf4;
    border: 2px dashed #86efac;
    padding: 20px;
    border-radius: 16px;
}

.add-grid{
    display: grid;
    grid-template-columns: 1.6fr 0.7fr 1fr auto;
    gap: 18px;
    align-items: end;
}

.btn-tambah{
    background: #2e7d32;
    color: white;
    border: none;
    padding: 12px 22px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
}

.btn-tambah:hover{
    background: #1b5e20;
}

/* ==== CART ITEM ==== */
.cart-item{
    background: white;
    border-left: 5px solid #2e7d32;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 15px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
    display: grid;
    grid-template-columns: 2fr 1fr 0.7fr 1fr auto;
    align-items: center;
    gap: 14px;
    font-size: 14px;
}

.cart-item input{
    padding: 6px 8px;
    width: 70px;
    border-radius: 8px;
}

/* ==== TOTAL BOX ==== */
.total-box{
    margin-top: 20px;
    padding: 22px;
    background: #f0fdf4;
    border-radius: 16px;
    border: 2px solid #86efac;
}

.total-row{
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    font-size: 15px;
}

.total-row strong{
    font-size: 19px;
    color: #1b5e20;
}

/* ==== BUTTON GROUP ==== */
.btn-group{
    margin-top: 26px;
    text-align: center;
}

.btn-success{
    background: #2e7d32;
    color: white;
    padding: 12px 32px;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    font-size: 15px;
}

.btn-secondary{
    background: #e2e8f0;
    color: #475569;
    padding: 12px 32px;
    border-radius: 12px;
    border: none;
    font-weight: 600;
}

/* ==== EMPTY ==== */
.empty{
    text-align: center;
    padding: 20px;
    color: #777;
    font-style: italic;
}

@media(max-width:900px){
    .form-grid, .add-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

<?php include(__DIR__ . '/../layout/sidebar.php'); ?>

<div class="main-content">

    <!-- HEADER -->
    <div class="page-header">
        <span>Tambah Pengadaan Barang</span>
        <a href="list.php"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <!-- CARD -->
    <div class="card">

        <div class="info-box">
            <strong>Informasi Penting:</strong><br>
            • Pilih vendor dan tambahkan beberapa barang dalam 1 transaksi.<br>
            • Subtotal, PPN (10%), dan Total dihitung otomatis oleh sistem.<br>
            • Klik "Tambah" untuk menambahkan barang ke daftar.
        </div>

        <!-- FORM -->
        <form method="POST" action="store.php" id="formPengadaan">

            <!-- VENDOR - PETUGAS -->
            <div class="form-grid">
                <div>
                    <label>Pilih Vendor *</label>
                    <select name="idvendor" id="idvendor" required>
                        <option value="">-- Pilih Vendor --</option>
                        <?php
                            $result = $conn->query("CALL sp_get_vendor_dropdown()");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['idvendor']}'>{$row['display_text']}</option>";
                            }
                            $result->free_result(); 
                            $conn->next_result();
                        ?>
                    </select>
                </div>

                <div>
                    <label>Petugas</label>
                    <input class="form-control" value="<?= $username ?>" readonly>
                    <input type="hidden" name="iduser" value="<?= $iduser ?>">
                </div>
            </div>

            <!-- ADD DETAIL -->
            <div class="add-section mt-4">
                <h6 style="font-weight:600; margin-bottom:15px;">Tambah Detail Barang</h6>

                <div class="add-grid">
                    <div>
                        <label>Pilih Barang *</label>
                        <select id="selectBarang">
                            <option value="">-- Pilih Barang --</option>
                            <?php
                            $result = $conn->query("CALL sp_get_barang_dropdown()");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['idbarang']}'
                                    data-nama='{$row['nama']}'
                                    data-satuan='{$row['nama_satuan']}'
                                    data-harga='{$row['harga']}'>
                                    {$row['display_text']}
                                </option>";
                            }
                            $result->free_result(); 
                            $conn->next_result();
                            ?>
                        </select>
                    </div>

                    <div>
                        <label>Jumlah *</label>
                        <input type="number" min="1" id="inputJumlah" class="form-control" placeholder="0">
                    </div>

                    <div>
                        <label>Harga Satuan</label>
                        <input class="form-control" id="displayHarga" readonly value="Rp 0">
                    </div>

                    <button type="button" id="btnTambahDetail" class="btn-tambah">Tambah</button>
                </div>
            </div>

            <!-- LIST BARANG -->
            <div id="daftarBarangContainer">
                <div class="empty">Belum ada barang yang ditambahkan</div>
            </div>

            <!-- TOTAL -->
            <div class="total-box">
                <div class="total-row">
                    <span>Subtotal</span>
                    <strong id="displaySubtotal">Rp 0</strong>
                </div>
                <div class="total-row">
                    <span>PPN (10%)</span>
                    <strong id="displayPPN">Rp 0</strong>
                </div>
                <div class="total-row">
                    <span>Total Pengadaan</span>
                    <strong id="displayTotal">Rp 0</strong>
                </div>
            </div>

            <div id="hiddenInputs"></div>
            <input type="hidden" name="status" value="P">

            <div class="btn-group">
                <button class="btn-success" id="btnSimpan" disabled>Simpan Pengadaan</button>
                <a href="list.php" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
let cart=[];

document.getElementById("selectBarang").addEventListener("change", e=>{
    let opt=e.target.selectedOptions[0];
    document.getElementById("displayHarga").value=opt.value ? "Rp "+new Intl.NumberFormat("id-ID").format(opt.dataset.harga):"Rp 0";
});

document.getElementById("btnTambahDetail").addEventListener("click",()=>{
    let sel=document.getElementById("selectBarang");
    let opt=sel.selectedOptions[0];
    let jml=parseInt(document.getElementById("inputJumlah").value)||0;

    if(!opt.value) return alert("Pilih barang!");
    if(jml<1) return alert("Jumlah tidak valid");
    if(cart.find(i=>i.id==opt.value)) return alert("Barang sudah ditambahkan!");

    let harga=parseInt(opt.dataset.harga);

    cart.push({id:opt.value, nama:opt.dataset.nama, satuan:opt.dataset.satuan, harga, jumlah:jml, subtotal: harga*jml});

    sel.value="";
    document.getElementById("inputJumlah").value="";
    document.getElementById("displayHarga").value="Rp 0";
    render();
});

function render(){
    let cont=document.getElementById("daftarBarangContainer");
    if(cart.length===0){
        cont.innerHTML='<div class="empty">Belum ada barang yang ditambahkan</div>';
        document.getElementById("btnSimpan").disabled=true;
        return;
    }

    cont.innerHTML=cart.map((item,i)=>`
        <div class="cart-item">
            <div>
                <strong>${item.nama}</strong><br>
                <small class="text-muted">${item.id} • ${item.satuan}</small>
            </div>

            <div>Rp ${new Intl.NumberFormat("id-ID").format(item.harga)}</div>

            <div>
                <input type="number" value="${item.jumlah}" min="1"
                    onchange="updateJumlah(${i},this.value)">
            </div>

            <div class="text-success fw-bold">
                Rp ${new Intl.NumberFormat("id-ID").format(item.subtotal)}
            </div>

            <button class="btn btn-danger btn-sm" onclick="removeItem(${i})">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `).join("");

    let subtotal=cart.reduce((a,b)=>a+b.subtotal,0);
    let ppn=Math.round(subtotal*0.10);

    document.getElementById("displaySubtotal").textContent="Rp "+subtotal.toLocaleString("id-ID");
    document.getElementById("displayPPN").textContent="Rp "+ppn.toLocaleString("id-ID");
    document.getElementById("displayTotal").textContent="Rp "+(subtotal+ppn).toLocaleString("id-ID");

    document.getElementById("hiddenInputs").innerHTML=cart.map(item=>`
        <input type="hidden" name="barang_id[]" value="${item.id}">
        <input type="hidden" name="harga_satuan[]" value="${item.harga}">
        <input type="hidden" name="jumlah[]" value="${item.jumlah}">
    `).join("");

    document.getElementById("btnSimpan").disabled=false;
}

function updateJumlah(i,val){
    let j=parseInt(val)||1;
    cart[i].jumlah=j;
    cart[i].subtotal=cart[i].harga*j;
    render();
}

function removeItem(i){
    cart.splice(i,1);
    render();
}
</script>

</body>
</html>
