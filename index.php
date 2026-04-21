<?php
session_start();
error_reporting(0);

// Tanpa isset, pesan error akan disembunyikan oleh error_reporting(0)
$action = $_GET['action'];

// --- FITUR LOGOUT ---
if ($action == 'logout') {
    session_destroy();
    $_SESSION['nama_pembeli'] = ""; // Mengosongkan memori nama agar langsung kembali ke form login
}

// Menggunakan array standar dan dipisah agar bisa di-loop dengan "for" biasa
$nama_buah = array("Apel", "Jeruk", "Mangga", "Pisang", "Anggur", "Semangka", "Melon", "Nanas", "Pepaya", "Alpukat", "Stroberi", "Kiwi", "Naga", "Durian", "Rambutan");
$info_buah = array(
    "Apel segar kaya akan serat dan vitamin C. Cocok untuk camilan sehat.", 
    "Jeruk manis kaya vitamin C untuk meningkatkan imun tubuh.", 
    "Mangga harum manis, cocok dibuat jus atau dimakan langsung.", 
    "Sumber energi cepat dan kalium yang baik untuk otot.", 
    "Anggur manis tanpa biji, kaya antioksidan.", 
    "Buah kaya air yang sangat menyegarkan di siang hari.", 
    "Melon manis dan segar, cocok untuk hidangan penutup.", 
    "Nanas segar, baik untuk pencernaan dan menyegarkan.", 
    "Sangat baik untuk melancarkan pencernaan.", 
    "Kaya akan lemak sehat dan cocok untuk diet atau jus.", 
    "Stroberi asam manis, kaya vitamin dan antioksidan.", 
    "Kiwi segar dengan kandungan vitamin C yang sangat tinggi.", 
    "Buah naga merah, sangat baik untuk kesehatan kulit.", 
    "Raja buah dengan rasa legit dan aroma khas.", 
    "Buah tropis manis dan berair."
);
$harga_buah = array(20000, 15000, 25000, 12000, 40000, 10000, 15000, 12000, 10000, 30000, 35000, 45000, 25000, 75000, 15000);
$gambar_buah = array(
    "https://images.unsplash.com/photo-1560806887-1e4cd0b6faa6?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1553279768-865429fa0078?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1603833665858-e61d17a86224?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1537640538966-79f369143f8f?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1587049352847-4d4b12404106?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1571575173700-afb9492e6a50?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1550258987-190a2d41a8ba?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1617112848504-03c00cb5ed01?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1523049673857-eb18f1d7b578?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1585059895524-72359e06138a?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1527325678964-54921661f888?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1632598858169-2cd1681577d6?w=200&h=150&fit=crop",
    "https://images.unsplash.com/photo-1629853381665-274fcce06cc0?w=200&h=150&fit=crop"
);

// --- 1. SISTEM LOGIN ---
$login_dikirim = $_POST['login_dikirim'];

if ($login_dikirim == "ya") {
    $_SESSION['nama_pembeli'] = $_POST['nama_pembeli'];
}

$nama_pembeli = $_SESSION['nama_pembeli'];

// --- 2. SISTEM KERANJANG ---
$keranjang_buah = $_SESSION['keranjang_buah'];
if ($keranjang_buah == "") {
    $_SESSION['keranjang_buah'] = array();
    $_SESSION['keranjang_jumlah'] = array();
    $_SESSION['ukuran_keranjang'] = 0;
}

if ($action == 'tambah_keranjang') {
    $buah_dipilih = $_GET['buah'];
    $jumlah_ditambah = $_GET['jumlah']; // Mengambil nilai kilogram dari URL
    
    // Jika jumlah tidak diset, default ke 1
    if ($jumlah_ditambah == "") {
        $jumlah_ditambah = 1;
    }
    
    $ketemu = 0;
    $jumlah_isi_keranjang = $_SESSION['ukuran_keranjang'];
    for ($i = 0; $i < $jumlah_isi_keranjang; $i = $i + 1) {
        if ($_SESSION['keranjang_buah'][$i] == $buah_dipilih) {
            $_SESSION['keranjang_jumlah'][$i] = $_SESSION['keranjang_jumlah'][$i] + $jumlah_ditambah; // Menambahkan sesuai inputan
            $ketemu = 1;
        }
    }
    
    if ($ketemu == 0) {
        if ($buah_dipilih == "") {
            // Abaikan jika tidak ada nama buah
        } else {
            $indeks_baru = $_SESSION['ukuran_keranjang'];
            $_SESSION['keranjang_buah'][$indeks_baru] = $buah_dipilih;
            $_SESSION['keranjang_jumlah'][$indeks_baru] = $jumlah_ditambah; // Menyimpan sesuai inputan
            $_SESSION['ukuran_keranjang'] = $indeks_baru + 1;
        }
    }
}

if ($action == 'kosongkan_keranjang') {
    $_SESSION['keranjang_buah'] = array();
    $_SESSION['keranjang_jumlah'] = array();
    $_SESSION['ukuran_keranjang'] = 0;
}

// --- 3. PROSES TRANSAKSI (CHECKOUT) ---
$pesan_transaksi = "";
$checkout_dikirim = $_POST['checkout_dikirim'];

if ($checkout_dikirim == "ya") {
    $jumlah_isi_keranjang = $_SESSION['ukuran_keranjang'];
    
    if ($jumlah_isi_keranjang == 0) {
        $pesan_transaksi = "Keranjang Anda masih kosong!";
    } else {
        $nama = $_POST['nama'];
        $_SESSION['pembeli_terakhir'] = $nama;

        $ekspedisi = $_POST['ekspedisi'];
        $voucher = $_POST['voucher'];

        $foto_nota = "default.jpg";
        $nama_foto = $_FILES['foto']['name'];
        
        if ($nama_foto == "") {
            // Tidak ada foto yang diupload
        } else {
            // Ada foto, langsung simpan di lokasi yang sama dengan file index.php
            $foto_nota = time() . "_" . $nama_foto;
            move_uploaded_file($_FILES['foto']['tmp_name'], $foto_nota);
        }

        // Perhitungan Ongkir berdasarkan pilihan Ekspedisi
        $ongkir = 0;
        if ($ekspedisi == "JNE") {
            $ongkir = 15000;
        }
        if ($ekspedisi == "JNT") {
            $ongkir = 12000;
        }
        if ($ekspedisi == "Sicepat") {
            $ongkir = 14000;
        }

        // Perhitungan Diskon Voucher
        $diskon = 0;
        if ($voucher == "BUAHSEGAR") {
            $diskon = $ongkir; // Diskon gratis ongkir (diskon sebesar ongkirnya)
        }

        $total_harga_buah = 0;
        $string_detail = ""; 

        for ($i = 0; $i < $jumlah_isi_keranjang; $i = $i + 1) {
            $b_nama = $_SESSION['keranjang_buah'][$i];
            $b_jumlah = $_SESSION['keranjang_jumlah'][$i];
            
            $harga_satuan = 0;
            for ($j = 0; $j < 15; $j = $j + 1) {
                if ($nama_buah[$j] == $b_nama) {
                    $harga_satuan = $harga_buah[$j];
                }
            }
            
            $subtotal = $harga_satuan * $b_jumlah;
            $total_harga_buah = $total_harga_buah + $subtotal;
            
            if ($string_detail == "") {
                $string_detail = $b_nama . "(" . $b_jumlah . " kg)";
            } else {
                $string_detail = $string_detail . ", " . $b_nama . "(" . $b_jumlah . " kg)";
            }
        }

        // Perhitungan Total Bayar Semua
        $total_bayar_semua = $total_harga_buah + $ongkir - $diskon;

        // Rangkum pesan ke file dan layar (Memasukkan unsur Diskon ke Nota)
        $info_tambahan = " | Ekspedisi: " . $ekspedisi . " | Voucher: " . $voucher . " | Diskon: Rp " . $diskon;
        $file = fopen("transaksi.txt", "a");
        fwrite($file, "NOTA PEMBELIAN: " . $nama . " | " . $string_detail . $info_tambahan . " | Total Tagihan: Rp " . $total_bayar_semua . " | Bukti: " . $foto_nota . "\n");
        fclose($file);

        // Susun pesan transaksi agar rapi di HTML
        $pesan_transaksi = "Pesanan atas nama <b>" . $nama . "</b> sedang diproses!<br><br>";
        $pesan_transaksi = $pesan_transaksi . "Daftar Buah: " . $string_detail . " (Rp " . $total_harga_buah . ")<br>";
        $pesan_transaksi = $pesan_transaksi . "Ekspedisi: " . $ekspedisi . " (Rp " . $ongkir . ")<br>";
        
        if ($diskon > 0) {
            $pesan_transaksi = $pesan_transaksi . "<span style='color:#d63031;'>Voucher Diterapkan! Diskon: - Rp " . $diskon . "</span><br>";
        } else {
            $pesan_transaksi = $pesan_transaksi . "<span style='color:#636e72;'>Tidak ada diskon / voucher tidak valid.</span><br>";
        }

        $pesan_transaksi = $pesan_transaksi . "<br><b style='font-size:20px; color:#00b894;'>Total Tagihan Akhir: Rp " . $total_bayar_semua . "</b>";
        
        $_SESSION['keranjang_buah'] = array();
        $_SESSION['keranjang_jumlah'] = array();
        $_SESSION['ukuran_keranjang'] = 0;
    }
}

// --- 4. PROSES POLLING ---
$vote_dikirim = $_POST['vote_dikirim'];

if ($vote_dikirim == "ya") {
    $pilihan = $_POST['buah_favorit'];
    $file_poll = fopen("polling.txt", "a");
    fwrite($file_poll, $pilihan . "\n");
    fclose($file_poll);
}

$suara_apel = 0;
$suara_jeruk = 0;
$suara_mangga = 0;
$total_suara = 0;

if (file_exists("polling.txt")) {
    $data_poll = file("polling.txt");
    
    $i = 0;
    while ($data_poll[$i] != null) {
        $p = $data_poll[$i];
        
        // Mengganti trim dengan menghilangkan karakter enter/newline manual
        $p = str_replace("\n", "", $p);
        $p = str_replace("\r", "", $p);
        
        if ($p == "Apel") {
            $suara_apel = $suara_apel + 1;
        }
        if ($p == "Jeruk") {
            $suara_jeruk = $suara_jeruk + 1;
        }
        if ($p == "Mangga") {
            $suara_mangga = $suara_mangga + 1;
        }
        
        if ($p != "") {
            $total_suara = $total_suara + 1;
        }
        
        $i = $i + 1;
    }
}

// --- PENGATURAN HALAMAN (Routing Dasar) ---
$page = $_GET['page'];

if ($page == "") {
    $page = 'home';
}

// Wajibkan pengisian nama jika belum login
if ($nama_pembeli == "") {
    $page = 'login';
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Buah Ceria</title>
    <!-- Menambahkan Font Google 'Nunito' agar lebih fun dan modern -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- BAGIAN CSS -->
    <style>
        :root {
            --primary: #ff7675;
            --secondary: #00b894;
            --accent: #fdcb6e;
            --text-dark: #2d3436;
        }
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #fffaf0; /* Warna dasar krem lembut */
            color: var(--text-dark);
            margin: 0; padding: 0;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
        }

        /* Background Shapes Animasi CSS (Modern Glassmorphism) */
        .bg-shape {
            position: fixed;
            border-radius: 50%;
            z-index: -1;
            filter: blur(90px);
            opacity: 0.7;
            animation: floatShape 12s infinite ease-in-out alternate;
        }
        .bg-shape1 {
            width: 450px; height: 450px;
            background: #ff7675; /* Warna koral/apel */
            top: -10%; left: -10%;
        }
        .bg-shape2 {
            width: 550px; height: 550px;
            background: #fdcb6e; /* Warna kuning/mangga */
            bottom: -15%; right: -10%;
            animation-delay: -4s;
        }
        .bg-shape3 {
            width: 400px; height: 400px;
            background: #00b894; /* Warna hijau segar */
            top: 30%; left: 40%;
            animation-delay: -8s;
        }
        
        @keyframes floatShape {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(60px, 40px) scale(1.15); }
        }

        .header {
            background-color: var(--primary);
            color: white;
            text-align: center;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
            z-index: 10;
        }
        .header h1 { margin: 0 0 10px 0; font-weight: 900; letter-spacing: 1px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);}
        .identitas { font-size: 14px; background: #d63031; display: inline-block; padding: 5px 20px; border-radius: 30px; font-weight: bold;}
        
        .nav-menu {
            display: flex; justify-content: center; gap: 15px; margin: 25px 0; flex-wrap: wrap;
        }
        .nav-menu a {
            text-decoration: none; background-color: var(--secondary); color: white;
            padding: 12px 25px; border-radius: 30px; font-weight: bold;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .nav-menu a:hover { 
            background-color: #00cec9; transform: translateY(-3px) scale(1.05); 
            box-shadow: 0 7px 14px rgba(0,0,0,0.2);
        }
        
        /* Animasi berkedip/napas pada badge keranjang */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); background-color: #ff4757; }
            100% { transform: scale(1); }
        }
        .badge {
            background: #d63031; color: white; border-radius: 50%; padding: 4px 8px; 
            font-size: 12px; position: absolute; top: -8px; right: -8px;
            animation: pulse 1.5s infinite; border: 2px solid white;
        }
        
        /* Glassmorphism Effect untuk Container */
        .container {
            max-width: 950px; margin: 0 auto; 
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 40px; border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            margin-bottom: 60px;
            margin-top: -15px; /* Sedikit overlap dengan header biar keren */
            position: relative; z-index: 5;
            animation: fadeInUP 0.8s ease;
        }
        
        @keyframes fadeInUP {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group { margin-bottom: 20px; text-align: left; }
        label { font-weight: bold; display: block; margin-bottom: 8px; color: #d63031;}
        input[type="text"], input[type="number"], select, input[type="file"] {
            width: 100%; padding: 12px; border: 2px solid #fab1a0; border-radius: 10px; 
            box-sizing: border-box; font-family: 'Nunito', sans-serif;
            transition: 0.3s; background: rgba(255,255,255,0.9);
        }
        input:focus, select:focus { border-color: var(--secondary); outline: none; box-shadow: 0 0 8px rgba(0,184,148,0.3);}
        
        button {
            background-color: var(--primary); color: white; border: none;
            padding: 15px 25px; border-radius: 12px; cursor: pointer; font-size: 16px; font-weight: bold; width: 100%;
            transition: all 0.3s; font-family: 'Nunito', sans-serif;
            box-shadow: 0 4px 6px rgba(255, 118, 117, 0.3);
        }
        button:hover { background-color: #d63031; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(214, 48, 49, 0.4); }
        button:active { transform: translateY(1px); }
        .btn-small { padding: 10px 15px; font-size: 14px; width: auto;}
        
        .grid-produk {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1));
            display: flex; flex-wrap: wrap; justify-content: center; gap: 20px;
            padding-top: 10px;
        }
        .produk-card {
            background: #fff; border: 3px solid transparent; border-radius: 15px;
            padding: 12px; text-align: center; width: 160px; cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            position: relative; overflow: hidden;
            opacity: 0; /* Untuk animasi via JS di awal */
        }
        .produk-card img { 
            width: 100%; height: 120px; object-fit: cover; border-radius: 10px; 
            transition: 0.5s;
        }
        .produk-card h3 { color: #e17055; margin: 12px 0 5px 0; font-size: 19px; font-weight: 800;}
        .harga-label { font-weight: bold; color: #00b894; font-size: 15px; background: #e8f8f5; padding: 5px; border-radius: 8px;}
        
        /* Efek Modal Info dengan transisi */
        .info-modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.7); z-index: 1000; display: none;
            justify-content: center; align-items: center;
            backdrop-filter: blur(5px);
        }
        .info-content {
            background: white; padding: 30px; border-radius: 20px; width: 340px;
            text-align: center; border: 5px solid var(--secondary); position: relative;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            transform: scale(0.7); opacity: 0; transition: all 0.3s ease-out;
        }
        .info-content.show { transform: scale(1); opacity: 1; }
        .close-info {
            position: absolute; top: -15px; right: -15px; cursor: pointer; 
            background: #d63031; color: white; border-radius: 50%;
            width: 35px; height: 35px; line-height: 35px; text-align: center;
            font-size: 20px; font-weight: bold; border: 3px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: 0.3s;
        }
        .close-info:hover { background: #2d3436; transform: rotate(90deg); }
        .img-modal {
            width: 100%; height: 180px; object-fit: cover; border-radius: 15px; margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);}
        th, td { border-bottom: 1px solid #eee; padding: 15px; text-align: left; }
        th { background-color: var(--accent); color: #d63031; text-transform: uppercase; font-size: 14px;}
        tr:hover td { background-color: #fcfcfc; }
        
        #iklan-pojok {
            position: fixed; bottom: 20px; right: 20px; background: #0984e3; color: white;
            padding: 25px; border-radius: 20px; width: 260px; display: none; z-index: 999;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3); border: 4px solid #74b9ff;
        }
        #tutup-pojok { background: #d63031; padding: 5px 12px; position: absolute; top: 10px; right: 10px; border-radius: 5px; width: auto; transition: 0.3s;}
        #tutup-pojok:hover { background: #2d3436; }
        
        /* Pesan Sukses Transaksi Animasi */
        .alert-success {
            background:#55efc4; color:#2d3436; padding:20px; border-radius:15px; 
            margin-bottom:25px; text-align:center; box-shadow: 0 8px 15px rgba(85, 239, 196, 0.4);
            border-left: 8px solid #00b894; animation: slideIn 0.5s ease;
        }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    </style>

    <!-- BAGIAN JAVASCRIPT & jQUERY -->
    <script>
        $(document).ready(function() {
            
            // Animasi Efek Muncul Berurutan (Staggered Fade-In) pada Kartu Produk
            $(".produk-card").each(function(index) {
                $(this).delay(100 * index).animate({opacity: 1}, 600);
            });

            // Animasi Interaktif Kartu Produk Saat Di-Hover
            $(".produk-card").hover(
                function() { 
                    $(this).css({"transform": "translateY(-10px) scale(1.03)", "border-color": "#00b894", "box-shadow": "0 15px 25px rgba(0,184,148,0.2)"}); 
                    $(this).find("img").css("transform", "scale(1.1)");
                },
                function() { 
                    $(this).css({"transform": "translateY(0) scale(1)", "border-color": "transparent", "box-shadow": "0 5px 15px rgba(0,0,0,0.05)"}); 
                    $(this).find("img").css("transform", "scale(1)");
                }
            );

            // Pop-up Modal Interaktif
            $(".produk-card").click(function() {
                var namaBuah = $(this).data("nama");
                var infoBuah = $(this).data("info");
                var hargaBuah = $(this).data("harga");
                var gambarBuah = $(this).data("gambar");

                // Simpan nama buah ke dalam elemen agar bisa dipanggil saat input berubah
                $("#modal-nama-buah").data("nama", namaBuah);
                
                $("#modal-nama-buah").text("🥭 " + namaBuah + " 🍎");
                $("#modal-info-buah").text(infoBuah);
                $("#modal-harga-buah").text("Harga: Rp " + hargaBuah + " / kg");
                $("#modal-gambar-buah").attr("src", gambarBuah);
                
                // Reset input jumlah kilogram ke 1
                $("#input-jumlah-buah").val(1);
                
                // Set parameter href untuk tombol keranjang
                $("#btn-tambah-keranjang").attr("href", "?page=home&action=tambah_keranjang&buah=" + namaBuah + "&jumlah=1");
                
                // Tampilkan overlay lalu berikan class untuk scale animation CSS
                $(".info-modal").css("display", "flex").hide().fadeIn(200, function(){
                    $(".info-content").addClass("show");
                });
            });

            // Mengganti link href secara dinamis ketika input jumlah kilogram diubah
            $("#input-jumlah-buah").on("input", function() {
                var namaBuah = $("#modal-nama-buah").data("nama");
                var jml = $(this).val();
                
                // Mencegah nilai minus atau kosong
                if (jml < 1 || jml == "") {
                    jml = 1;
                }
                
                $("#btn-tambah-keranjang").attr("href", "?page=home&action=tambah_keranjang&buah=" + namaBuah + "&jumlah=" + jml);
            });

            // Menutup Modal Interaktif
            function tutupModal() {
                $(".info-content").removeClass("show");
                setTimeout(function(){
                    $(".info-modal").fadeOut(200);
                }, 300);
            }
            $(".close-info").click(tutupModal);
            $(".info-modal").click(function(event) { if (event.target === this) { tutupModal(); } });

            // Efek Iklan Muncul Tiba-tiba (Bouncing)
            setTimeout(function() { 
                $("#iklan-pojok").slideDown(500).animate({bottom: '30px'}, 150).animate({bottom: '20px'}, 150); 
            }, 2500);
            $("#tutup-pojok").click(function() { $("#iklan-pojok").slideUp(300); });
        });
    </script>
</head>
<body>

    <!-- Element CSS untuk Background Animasi Bergerak -->
    <div class="bg-shape bg-shape1"></div>
    <div class="bg-shape bg-shape2"></div>
    <div class="bg-shape bg-shape3"></div>

    <div class="header">
        <h1>🍍 Toko Buah Ceria 🍉</h1>
        <div class="identitas">Dibuat oleh: <b>Nama Kamu</b> | Kelas: <b>XII RPL / SMA</b></div>
    </div>

    <div class="container">
        
        <?php if ($page == 'login') { ?>
            <!-- HALAMAN LOGIN AWAL -->
            <div style="text-align:center; margin-bottom: 20px;">
                <h2 style="color: var(--primary); font-size: 28px;">Selamat Datang! 👋</h2>
                <p style="color: #636e72; font-size: 16px;">Silakan masukkan nama Anda untuk memulai pesanan buah segar kami.</p>
            </div>
            
            <form method="POST" action="?page=home" style="max-width: 400px; margin: 0 auto; background: white; padding: 35px; border-radius:20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 2px solid #ecf0f1;">
                <input type="hidden" name="login_dikirim" value="ya">
                <div class="form-group">
                    <label>Nama Anda:</label>
                    <input type="text" name="nama_pembeli" placeholder="Ketik nama lengkap..." required autocomplete="off">
                </div>
                <button type="submit">Mulai Belanja 🚀</button>
            </form>

        <?php } else { ?>
            <!-- MENU NAVIGASI (Hanya muncul jika sudah ada nama_pembeli) -->
            <?php 
                $jml_keranjang = 0;
                $jumlah_isi_keranjang = $_SESSION['ukuran_keranjang'];
                for ($i = 0; $i < $jumlah_isi_keranjang; $i = $i + 1) {
                    $jml_keranjang = $jml_keranjang + $_SESSION['keranjang_jumlah'][$i];
                }
            ?>
            <div style="background: rgba(253, 203, 110, 0.2); border-radius: 15px; padding: 15px; margin-bottom: 25px;">
                <p style="text-align: center; font-size: 18px; margin: 0;">Halo, <b style="color:var(--primary);"><?php echo $nama_pembeli; ?></b>! Mau buah apa hari ini? 🍓</p>
            </div>
            
            <div class="nav-menu">
                <a href="?page=home">🏠 Katalog Buah</a>
                <a href="?page=transaksi">
                    🛒 Keranjang Checkout
                    <?php if ($jml_keranjang > 0) { ?> 
                        <span class="badge"><?php echo $jml_keranjang; ?></span> 
                    <?php } ?>
                </a>
                <a href="?page=polling">📊 Polling Pilihan</a>
                <a href="?action=logout" style="background:#ff4757;">🔴 Keluar</a>
            </div>
            <hr style="border: none; border-top: 2px dashed #dfe6e9; margin-bottom:30px;">
        
            <?php if ($page == 'home') { ?>
                <!-- HALAMAN HOME (15 PRODUK) -->
                <p style="text-align:center; color:#636e72; font-weight:bold;"><i>👉 Klik gambar buah untuk melihat info dan membelinya!</i></p>
                <div class="grid-produk">
                    <?php 
                    for ($i = 0; $i < 15; $i = $i + 1) { 
                    ?>
                        <div class="produk-card" 
                             data-nama="<?php echo $nama_buah[$i]; ?>" 
                             data-info="<?php echo $info_buah[$i]; ?>"
                             data-harga="<?php echo $harga_buah[$i]; ?>"
                             data-gambar="<?php echo $gambar_buah[$i]; ?>">
                            
                            <!-- Wrapper untuk zoom gambar -->
                            <div style="overflow:hidden; border-radius:10px;">
                                <img src="<?php echo $gambar_buah[$i]; ?>" alt="<?php echo $nama_buah[$i]; ?>">
                            </div>
                            <h3><?php echo $nama_buah[$i]; ?></h3>
                            <p class="harga-label">Rp <?php echo $harga_buah[$i]; ?>/kg</p>
                        </div>
                    <?php } ?>
                </div>

                <!-- Modal Info Buah -->
                <div class="info-modal">
                    <div class="info-content">
                        <span class="close-info">X</span>
                        <h2 id="modal-nama-buah" style="color: var(--secondary); margin-top:5px; font-weight:900;">Nama Buah</h2>
                        <img id="modal-gambar-buah" src="" alt="Gambar Buah" class="img-modal">
                        <p id="modal-info-buah" style="color: #636e72; line-height: 1.5;">Informasi detail tentang buah akan muncul di sini.</p>
                        <h4 id="modal-harga-buah" style="color: #d63031; font-size: 20px; background: #ffeaa7; padding: 10px; border-radius: 10px; margin-bottom: 10px;">Harga: -</h4>
                        
                        <!-- Tambahan Input Kilogram -->
                        <div style="margin-bottom: 15px; padding: 10px; background: #fafafa; border-radius: 10px; border: 1px solid #dfe6e9;">
                            <label for="input-jumlah-buah" style="display:inline-block; color:#2d3436; font-weight:bold; margin-right:10px; margin-bottom:0;">Jumlah (Kg):</label>
                            <input type="number" id="input-jumlah-buah" value="1" min="1" style="width:80px; display:inline-block; padding:8px; border-radius:8px; border:2px solid var(--secondary); text-align:center;">
                        </div>

                        <a id="btn-tambah-keranjang" href="#" style="display:inline-block; margin-top:5px; background:var(--primary); color:white; padding:15px 25px; text-decoration:none; border-radius:12px; font-weight:bold; transition:0.3s; width:80%; box-shadow: 0 4px 6px rgba(255, 118, 117, 0.4);">+ Masukkan Keranjang 🛒</a>
                    </div>
                </div>

            <?php } else if ($page == 'transaksi') { ?>
                
                <!-- HALAMAN TRANSAKSI / KERANJANG -->
                <h2 style="text-align:center; color: var(--secondary); font-weight: 900; margin-bottom: 30px;">Keranjang Belanja Anda</h2>
                
                <?php if ($pesan_transaksi == "") { } else { ?>
                    <div class="alert-success">
                        <h3 style="margin:0 0 5px 0;">🎉 Yeay!</h3>
                        <!-- Menggunakan span agar pesan tidak terlalu berantakan -->
                        <span style="font-size: 16px; line-height: 1.5; display: block; margin-top:10px;">
                            <?php echo $pesan_transaksi; ?>
                        </span>
                    </div>
                <?php } ?>
                
                <?php 
                $jumlah_isi_keranjang = $_SESSION['ukuran_keranjang'];
                if ($jumlah_isi_keranjang == 0) { 
                ?>
                    <div style="text-align:center; padding: 60px 40px; background: #fafafa; border-radius:20px; border:3px dashed #dfe6e9;">
                        <span style="font-size: 50px;">🛒</span>
                        <p style="font-size: 20px; color:#636e72; margin-top: 10px;">Keranjang Anda masih sepi...</p>
                        <a href="?page=home" style="display:inline-block; margin-top:15px; background:var(--secondary); color:white; padding:12px 25px; text-decoration:none; border-radius:30px; font-weight:bold; box-shadow:0 5px 15px rgba(0,184,148,0.3);">Lihat Katalog Buah</a>
                    </div>
                <?php } else { ?>
                    
                    <table>
                        <tr>
                            <th>Nama Buah</th>
                            <th>Harga / Kg</th>
                            <th>Jml (Kg)</th>
                            <th>Subtotal</th>
                        </tr>
                        <?php 
                        $total_akhir_buah = 0;
                        for ($i = 0; $i < $jumlah_isi_keranjang; $i = $i + 1) { 
                            $b_nama = $_SESSION['keranjang_buah'][$i];
                            $b_jumlah = $_SESSION['keranjang_jumlah'][$i];
                            
                            $harga_satuan = 0;
                            for ($j = 0; $j < 15; $j = $j + 1) {
                                if ($nama_buah[$j] == $b_nama) {
                                    $harga_satuan = $harga_buah[$j];
                                }
                            }
                            
                            $sub = $harga_satuan * $b_jumlah;
                            $total_akhir_buah = $total_akhir_buah + $sub;
                        ?>
                        <tr>
                            <td><b style="color:var(--primary); font-size:16px;">🍉 <?php echo $b_nama; ?></b></td>
                            <td>Rp <?php echo $harga_satuan; ?></td>
                            <td><span style="background:#dfe6e9; padding: 4px 12px; border-radius: 20px; font-weight:bold;"><?php echo $b_jumlah; ?></span></td>
                            <td style="font-weight:bold;">Rp <?php echo $sub; ?></td>
                        </tr>
                        <?php } ?>
                        <tr style="background-color: #fff9e6;">
                            <td colspan="3" style="text-align:right; font-weight:900; font-size: 16px;">Total Belanja Buah:</td>
                            <td style="font-weight:900; color:#d63031; font-size:16px;">Rp <?php echo $total_akhir_buah; ?></td>
                        </tr>
                    </table>
                    <div style="text-align:right; margin-bottom: 30px;">
                         <a href="?page=transaksi&action=kosongkan_keranjang" class="btn-small" style="background:#ff4757; color:white; text-decoration:none; padding:10px 20px; border-radius:30px; font-weight:bold; box-shadow: 0 4px 10px rgba(255, 71, 87, 0.3);">Kosongkan Keranjang 🗑️</a>
                    </div>

                    <form method="POST" action="?page=transaksi" enctype="multipart/form-data" style="background: white; padding: 35px; border-radius:20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 2px solid #ecf0f1;">
                        <input type="hidden" name="checkout_dikirim" value="ya">
                        <h3 style="margin-top:0; color:var(--secondary); font-weight: 900;">📋 Konfirmasi Checkout</h3>
                        <p style="color:#636e72; font-size:14px; margin-bottom: 20px;">Silakan lengkapi opsi pengiriman dan upload bukti transfer untuk menyelesaikan pesanan.</p>
                        
                        <div class="form-group">
                            <label>Nama Pembeli:</label>
                            <input type="text" name="nama" value="<?php echo $nama_pembeli; ?>" required>
                        </div>

                        <!-- Tambahan Form Ekspedisi -->
                        <div class="form-group">
                            <label>Ekspedisi Pengiriman:</label>
                            <select name="ekspedisi" required style="cursor:pointer;">
                                <option value="">-- Pilih Jasa Kirim --</option>
                                <option value="JNE">JNE Reguler (Rp 15.000)</option>
                                <option value="JNT">J&T Express (Rp 12.000)</option>
                                <option value="Sicepat">Sicepat (Rp 14.000)</option>
                            </select>
                        </div>

                        <!-- Tambahan Form Kode Voucher -->
                        <div class="form-group">
                            <label>Kode Voucher (Diskon Ongkir):</label>
                            <input type="text" name="voucher" placeholder="Masukkan kode BUAHSEGAR (Opsional)...">
                        </div>

                        <div class="form-group">
                            <label>Upload Bukti Pembayaran (Nota):</label>
                            <input type="file" name="foto" accept="image/*" required style="background: #fdfdfd; cursor:pointer;">
                        </div>
                        <button type="submit" style="background-color: var(--secondary); box-shadow: 0 4px 6px rgba(0, 184, 148, 0.4);">Selesaikan Pembayaran Sekarang 💸</button>
                    </form>
                <?php } ?>

            <?php } else if ($page == 'polling') { ?>
                
                <!-- HALAMAN POLLING -->
                <h2 style="text-align:center; color: var(--secondary); font-weight: 900;">📊 Polling Buah Terfavorit</h2>
                <p style="text-align:center; color:#636e72; margin-bottom: 30px;">Bantu kami menentukan buah apa yang paling disukai pelanggan!</p>
                
                <?php 
                $pembeli_terakhir = $_SESSION['pembeli_terakhir'];
                
                if ($pembeli_terakhir == "") { 
                ?>
                    <div style="text-align:center; padding: 50px; background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-left: 8px solid #ff7675;">
                        <h3 style="color: #d63031; font-size:24px;">Oops! ⚠️</h3>
                        <p style="font-size: 16px; color: #2d3436;">Hanya pelanggan yang telah menyelesaikan <b style="color:var(--secondary)">Checkout</b> yang dapat mengisi polling.</p>
                        <a href="?page=home" style="display:inline-block; margin-top:15px; background:var(--primary); color:white; padding:10px 25px; text-decoration:none; border-radius:30px; font-weight:bold;">Mulai Belanja</a>
                    </div>
                <?php } else { ?>
                    <div style="display:flex; gap: 30px; justify-content: center; flex-wrap: wrap;">
                        
                        <div style="background: white; padding: 30px; border-radius: 20px; width: 320px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-top: 6px solid var(--accent);">
                            <h3 style="margin-top:0; color:#e17055;">Suara Anda</h3>
                            <p style="background: #fdfdfd; padding: 10px; border-radius:10px; border: 1px solid #eee;">Pengisi: <b style="color:var(--primary);"><?php echo $pembeli_terakhir; ?></b></p>
                            
                            <form method="POST" action="?page=polling" style="margin-top:20px;">
                                <input type="hidden" name="vote_dikirim" value="ya">
                                
                                <div style="margin-bottom:15px; padding:10px; border-radius:10px; background:#fafafa; border:1px solid #ddd;">
                                    <label style="margin:0; cursor:pointer; color:#2d3436;"><input type="radio" name="buah_favorit" value="Apel" required style="width:auto; margin-right:10px;"> 🍎 Buah Apel</label>
                                </div>
                                <div style="margin-bottom:15px; padding:10px; border-radius:10px; background:#fafafa; border:1px solid #ddd;">
                                    <label style="margin:0; cursor:pointer; color:#2d3436;"><input type="radio" name="buah_favorit" value="Jeruk" style="width:auto; margin-right:10px;"> 🍊 Buah Jeruk</label>
                                </div>
                                <div style="margin-bottom:25px; padding:10px; border-radius:10px; background:#fafafa; border:1px solid #ddd;">
                                    <label style="margin:0; cursor:pointer; color:#2d3436;"><input type="radio" name="buah_favorit" value="Mangga" style="width:auto; margin-right:10px;"> 🥭 Buah Mangga</label>
                                </div>
                                
                                <button type="submit">Kirim Suara ✅</button>
                            </form>
                        </div>

                        <div style="background: white; padding: 30px; border-radius: 20px; width: 320px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-top: 6px solid #74b9ff;">
                            <h3 style="margin-top:0; color:#0984e3;">Live Hasil Polling</h3>
                            <p style="background: #fdfdfd; padding: 10px; border-radius:10px; border: 1px solid #eee;">Total Partisipasi: <b><?php echo $total_suara; ?> Suara</b></p>
                            
                            <?php 
                            $persen_apel = 0;
                            $persen_jeruk = 0;
                            $persen_mangga = 0;
                            if ($total_suara > 0) {
                                $persen_apel = (int) (($suara_apel / $total_suara) * 100);
                                $persen_jeruk = (int) (($suara_jeruk / $total_suara) * 100);
                                $persen_mangga = (int) (($suara_mangga / $total_suara) * 100);
                            }
                            ?>
                            
                            <div style='margin-bottom:20px; margin-top:20px;'>
                                <div style="display:flex; justify-content:space-between; font-weight:bold; margin-bottom:5px;"><span>🍎 Apel (<?php echo $suara_apel; ?>)</span> <span><?php echo $persen_apel; ?>%</span></div>
                                <div style='background:#dfe6e9; border-radius:10px; height:20px; width:100%; overflow:hidden;'>
                                    <div style='background: linear-gradient(90deg, #ff7675, #d63031); height:100%; width:<?php echo $persen_apel; ?>%; border-radius:10px; transition: width 1s ease-in-out;'></div>
                                </div>
                            </div>

                            <div style='margin-bottom:20px;'>
                                <div style="display:flex; justify-content:space-between; font-weight:bold; margin-bottom:5px;"><span>🍊 Jeruk (<?php echo $suara_jeruk; ?>)</span> <span><?php echo $persen_jeruk; ?>%</span></div>
                                <div style='background:#dfe6e9; border-radius:10px; height:20px; width:100%; overflow:hidden;'>
                                    <div style='background: linear-gradient(90deg, #fdcb6e, #e17055); height:100%; width:<?php echo $persen_jeruk; ?>%; border-radius:10px; transition: width 1s ease-in-out;'></div>
                                </div>
                            </div>

                            <div style='margin-bottom:20px;'>
                                <div style="display:flex; justify-content:space-between; font-weight:bold; margin-bottom:5px;"><span>🥭 Mangga (<?php echo $suara_mangga; ?>)</span> <span><?php echo $persen_mangga; ?>%</span></div>
                                <div style='background:#dfe6e9; border-radius:10px; height:20px; width:100%; overflow:hidden;'>
                                    <div style='background: linear-gradient(90deg, #00b894, #00cec9); height:100%; width:<?php echo $persen_mangga; ?>%; border-radius:10px; transition: width 1s ease-in-out;'></div>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php } ?>
            <?php } ?>

        <?php } ?>
    </div>

    <!-- IKLAN POJOK KANAN BAWAH SAJA -->
    <div id="iklan-pojok">
        <button id="tutup-pojok">X</button>
        <h4 style="margin-top:0; font-size:20px;">🎁 Voucher Gratis!</h4>
        <p style="margin-bottom:0;">Gunakan kode <b style="background:#ffeaa7; color:#2d3436; padding:3px 8px; border-radius:5px;">BUAHSEGAR</b> untuk gratis ongkir se-Indonesia. <br><br><i>*Khusus hari ini!</i></p>
    </div>

</body>
</html>