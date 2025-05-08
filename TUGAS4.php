<?php

$gaji_pokok = 4500000;
$tunjangan_transportasi = 500000;
$tunjangan_makan = 300000;
$total_tunjangan = $tunjangan_transportasi + $tunjangan_makan;
$pendapatan_kotor = $gaji_pokok + $total_tunjangan;
$pajak_penghasilan = 0.1 * $pendapatan_kotor;
$bpjs = 200000;
$gaji_bersih = $pendapatan_kotor - $pajak_penghasilan - $bpjs;

define("GARIS", "==========================================");
echo "<h1>Tugas Pertemuan 4</h1>";
echo "<h3> Program php Menghitung</h3>";
echo GARIS;
echo "<pre style='font-size:20px;'>";
echo "Total Tunjangan      = Rp " . number_format($total_tunjangan, 0, ',', '.') . "\n";
echo "Pendapatan Kotor     = Rp " . number_format($pendapatan_kotor, 0, ',', '.') . "\n";
echo "Pajak Penghasilan    = Rp " . number_format($pajak_penghasilan, 0, ',', '.') . "\n";
echo "Gaji Bersih          = Rp " . number_format($gaji_bersih, 0, ',', '.') . "\n";
echo "</pre>";
echo GARIS;
?>