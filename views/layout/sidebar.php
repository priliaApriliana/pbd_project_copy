<?php
$current_page = basename($_SERVER['PHP_SELF']);
$username = $_SESSION['user']['username'] ?? 'Admin';
$role = $_SESSION['user']['role'] ?? 'Super Admin';
?>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="/pbd_project_copy/assets/icons/bootstrap-icons.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

/* Sidebar Container */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100vh;
    background: linear-gradient(180deg, #1e6b2a 0%, #2e7d32 100%);
    padding: 0;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1000;
    box-shadow: 4px 0 20px rgba(0,0,0,0.1);
    font-family: 'Poppins', sans-serif;
}

/* Custom Scrollbar */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.1);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.3);
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.5);
}

/* User Profile Section */
.sidebar-profile {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    padding: 24px 20px;
    margin: 20px 15px;
    border-radius: 16px;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.2);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.profile-avatar {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #fff, #e8f5e9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-size: 32px;
    color: #2e7d32;
    border: 3px solid rgba(255,255,255,0.3);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.profile-name {
    color: white;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 4px;
    letter-spacing: 0.5px;
}

.profile-role {
    color: rgba(255,255,255,0.85);
    font-size: 13px;
    font-weight: 500;
    padding: 4px 12px;
    background: rgba(255,255,255,0.15);
    border-radius: 20px;
    display: inline-block;
}

/* Menu Section */
.sidebar-menu {
    padding: 10px 15px 30px;
}

.menu-section-title {
    color: rgba(255,255,255,0.6);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    padding: 20px 15px 10px;
    margin-top: 10px;
}

.menu-item {
    display: flex;
    align-items: center;
    padding: 14px 18px;
    margin-bottom: 6px;
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-size: 15px;
    font-weight: 500;
    position: relative;
    overflow: hidden;
}

.menu-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: white;
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.menu-item:hover {
    background: rgba(255,255,255,0.15);
    color: white;
    transform: translateX(5px);
}

.menu-item:hover::before {
    transform: scaleY(1);
}

.menu-item.active {
    background: linear-gradient(135deg, rgba(255,255,255,0.25), rgba(255,255,255,0.15));
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-left: 4px solid white;
}

.menu-item i {
    font-size: 20px;
    width: 28px;
    margin-right: 14px;
    text-align: center;
}

.menu-item span {
    flex: 1;
}

/* Menu Icons Enhancement */
.menu-item i.bi-speedometer2 { color: #fff59d; }
.menu-item i.bi-person-badge { color: #a5d6a7; }
.menu-item i.bi-people-fill { color: #90caf9; }
.menu-item i.bi-building-fill-add { color: #ffcc80; }
.menu-item i.bi-rulers { color: #ce93d8; }
.menu-item i.bi-box-seam-fill { color: #80deea; }
.menu-item i.bi-percent { color: #ffab91; }
.menu-item i.bi-cart-check-fill { color: #f48fb1; }
.menu-item i.bi-house-fill { color: #aed581; }
.menu-item i.bi-receipt { color: #81d4fa; }
.menu-item i.bi-arrow-return-left { color: #ffcc80; }
.menu-item i.bi-layers-fill { color: #b39ddb; }

/* Active state - reset icon color to white */
.menu-item.active i {
    color: white !important;
}

/* Logout Button */
.menu-item.logout {
    background: rgba(244,67,54,0.2);
    border: 1px solid rgba(244,67,54,0.3);
    color: #ffcdd2;
    margin-top: 20px;
}

.menu-item.logout:hover {
    background: #f44336;
    color: white;
    border-color: #f44336;
}

.menu-item.logout i {
    color: #ffcdd2;
}

.menu-item.logout:hover i {
    color: white;
}

/* Responsive */
@media (max-width: 992px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
}

/* Badge/Counter */
.menu-badge {
    background: #f44336;
    color: white;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 12px;
    margin-left: auto;
}

/* Divider */
.menu-divider {
    height: 1px;
    background: rgba(255,255,255,0.15);
    margin: 15px 15px;
}
</style>

<!-- Sidebar HTML -->
<div class="sidebar">
    <!-- User Profile -->
    <div class="sidebar-profile">
        <div class="profile-avatar">
            <i class="bi bi-person-circle"></i>
        </div>
        <div class="profile-name"><?= htmlspecialchars($username) ?></div>
        <div class="profile-role"><?= htmlspecialchars($role) ?></div>
    </div>
    
    <!-- Menu -->
    <div class="sidebar-menu">
        <!-- Dashboard -->
        <a href="/pbd_project_copy/views/dashboard.php" class="menu-item <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        
        <div class="menu-divider"></div>
        
        <!-- Data Master Section -->
        <div class="menu-section-title">Data Master</div>
        
        <a href="/pbd_project_copy/views/role/list.php" class="menu-item <?= $current_page === 'role/list.php' ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i>
            <span>Role</span>
        </a>
        
        <a href="/pbd_project_copy/views/user/list.php" class="menu-item <?= $current_page === 'user/list.php' ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i>
            <span>User</span>
        </a>
        
        <a href="/pbd_project_copy/views/vendor/list.php" class="menu-item <?= $current_page === 'vendor/list.php' ? 'active' : '' ?>">
            <i class="bi bi-building-fill-add"></i>
            <span>Vendor</span>
        </a>
        
        <a href="/pbd_project_copy/views/satuan/list.php" class="menu-item <?= $current_page === 'satuan/list.php' ? 'active' : '' ?>">
            <i class="bi bi-rulers"></i>
            <span>Satuan</span>
        </a>
        
        <a href="/pbd_project_copy/views/barang/list.php" class="menu-item <?= $current_page === 'barang/list.php' ? 'active' : '' ?>">
            <i class="bi bi-box-seam-fill"></i>
            <span>Barang</span>
        </a>
        
        <a href="/pbd_project_copy/views/margin_penjualan/list.php" class="menu-item <?= $current_page === 'margin_penjualan/list.php' ? 'active' : '' ?>">
            <i class="bi bi-percent"></i>
            <span>Margin Penjualan</span>
        </a>
        
        <div class="menu-divider"></div>
        
        <!-- Transaksi Section -->
        <div class="menu-section-title">Transaksi</div>
        
        <a href="/pbd_project_copy/views/pengadaan/list.php" class="menu-item <?= $current_page === 'pengadaan/list.php' ? 'active' : '' ?>">
            <i class="bi bi-cart-check-fill"></i>
            <span>Pengadaan</span>
        </a>
        
        <a href="/pbd_project_copy/views/penerimaan/list.php" class="menu-item <?= $current_page === 'penerimaan/list.php' ? 'active' : '' ?>">
            <i class="bi bi-house-fill"></i>
            <span>Penerimaan</span>
        </a>
        
        <a href="/pbd_project_copy/views/penjualan/list.php" class="menu-item <?= $current_page === 'penjualan/list.php' ? 'active' : '' ?>">
            <i class="bi bi-receipt"></i>
            <span>Penjualan</span>
        </a>
        
        <a href="/pbd_project_copy/views/retur/list.php" class="menu-item <?= $current_page === 'retur/list.php' ? 'active' : '' ?>">
            <i class="bi bi-arrow-return-left"></i>
            <span>Retur</span>
        </a>
        
        <a href="/pbd_project_copy/views/kartu_stok/list.php" class="menu-item <?= $current_page === 'kartu_stok/list.php' ? 'active' : '' ?>">
            <i class="bi bi-layers-fill"></i>
            <span>Kartu Stok</span>
        </a>
        
        <div class="menu-divider"></div>
        
        <!-- Logout -->
        <a href="../../scripts/logout.php" class="menu-item logout" onclick="return confirm('Yakin ingin logout?')">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<script>
// Highlight active menu based on current URL
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && currentPath.includes(href.split('/').pop())) {
            item.classList.add('active');
        }
    });
});

// Mobile toggle (optional)
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('show');
}
</script>