<div class="sidebar">
    <h2>Admin Paneli</h2>
    <a href="index.php">Dashboard</a>
    <a href="add_product.php">Ürün Ekle</a>
    <a href="add_admin.php">Admin Ekle</a>
    <a href="../logout.php">Çıkış Yap</a>
</div>

<style>
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 220px;
        background-color: rgba(0, 0, 0, 0.85);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 20px;
        z-index: 1000;
    }
    .sidebar a {
        width: 180px;
        margin: 10px 0;
        padding: 12px;
        text-align: center;
        color: #fff;
        background-color: #333;
        border: 2px solid #555;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s;
    }
    .sidebar a:hover {
        background-color: #444;
        border-color: #777;
    }
    .sidebar h2 {
        color: #fff;
        margin-bottom: 30px;
        font-size: 24px;
        border-bottom: 2px solid #777;
        padding-bottom: 10px;
    }
</style>
