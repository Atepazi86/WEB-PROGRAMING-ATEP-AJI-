<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST["nama"];
    $kode_kereta = $_POST["kode_kereta"];
    $kelas = $_POST["kelas"];
    $jumlah = $_POST["jumlah"];
    $harga_tiket = 0;
    $nama_kereta = "";
    $tiket_tersedia = true;

    if ($kode_kereta == "ARGO") {
        $nama_kereta = "Argo Parahyangan";
        $harga_tiket = ($kelas == "Eksekutif") ? 120000 : 80000;
    } elseif ($kode_kereta == "LOKAL") {
        $nama_kereta = "Lokal Bandung Raya";
        $harga_tiket = ($kelas == "Ekonomi") ? 15000 : 0;
        $tiket_tersedia = ($harga_tiket > 0);
    } elseif ($kode_kereta == "SERAYU") {
        $nama_kereta = "Serayu Malam";
        if ($kelas == "Ekonomi") {
            $harga_tiket = 35000;
        } elseif ($kelas == "Bisnis") {
            $harga_tiket = 60000;
        } else {
            $tiket_tersedia = false;
        }
    }

    echo "<h2>Detail Pemesanan Tiket Tasikmalaya - Bandung </h2><pre>";
    echo "=================================\n";
    echo "Nama           : " . $nama . "\n";
    echo "Nama Kereta    : " . $nama_kereta . "\n";
    echo "Harga Tiket    : " . $harga_tiket . "\n";
    echo "Kelas          : " . $kelas . "\n";
    echo "Jumlah Tiket   : " . $jumlah . "\n";

    if ($tiket_tersedia) {
        $total = $harga_tiket * $jumlah;
        echo "Total Harga    : " . $total . "\n";
    } else {
        echo "Tiket tidak tersedia!\n";
    }
    
    echo "=================================";
    echo "</pre>";
    
    echo '<button onclick="window.history.back()">Kembali</button>';
}
?>
