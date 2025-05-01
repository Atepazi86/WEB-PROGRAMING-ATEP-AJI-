<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk SPBU</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .struk {
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 20px;
            width: 320px;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            white-space: pre-line;
        }
        .center {
            text-align: center;
        }
        .line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .catatan {
            font-size: 12px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="struk">
<?php

define("NAMA_SPBU", "SPBU PERTAMINA JAMANIS");
define("ALAMAT_SPBU", "JL.RAJAPOLAH, JAMANIS, KAB TASIKMALAYA");
define("PRODUK", "PERTAMAX");
define("HARGA_PER_LITER", 12000);
define("NAMA_OPERATOR", "ATEP");
define("CATATAN_SUBSIDI", "PERTAMAX Series dan Dex Series\nSubsidi hanya untuk yang berhak\nMenerima.");


$shift = 4;
$nomor_transaksi = 66666;
$tanggal = "01/05/2025";
$waktu = "10:04:41";
$nomor_pompa = 4;
$volume = 20;
$total_harga = HARGA_PER_LITER * $volume;
$metode_pembayaran = "CASH";
$jumlah_bayar = 240000;


echo "<div class='center'><strong>PERTAMINA</strong></div>";
echo "<div class='center'><strong>34.40229</strong></div>";
echo "<div class='center'><strong>" . NAMA_SPBU . "</strong></div>";
echo "<div class='center'>" . ALAMAT_SPBU . "</div>";
echo "Shift : " . $shift . "<br>";
echo "No. Trans : " . $nomor_transaksi . "<br>";
echo "<div class='line'></div>";
echo "Waktu : " . $tanggal . " " . $waktu . "<br>";
echo "<div class='line'></div>";
echo "Pulau/Pompa : " . $nomor_pompa . "<br>";
echo "Nama Produk : " . PRODUK . "<br>";
echo "Harga/Liter : Rp. " . number_format(HARGA_PER_LITER, 0, ',', '.') . "<br>";
echo "Volume : (L) " . $volume . "<br>";
echo "Total Harga : Rp. " . number_format($total_harga, 0, ',', '.') . "<br>";
echo "Operator : " . NAMA_OPERATOR . "<br>";
echo "<div class='line'></div>";
echo $metode_pembayaran . "<br>";
echo "Rp. " . number_format($jumlah_bayar, 0, ',', '.') . "<br>";
echo "<div class='line'></div>";
echo "<div class='catatan center'>" . nl2br(CATATAN_SUBSIDI) . "</div>";
?>
</div>

</body>
</html>
