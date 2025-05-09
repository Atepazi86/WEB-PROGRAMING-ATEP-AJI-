<!DOCTYPE html>
<html>
<head>
    <title>Menghitung Balok</title>
</head>
<body>
    <h1>Menghitung Balok</h1>
    <form method="post">
        Panjang <br><input type="number" name="panjang" required><br>
        Lebar <br><input type="number" name="lebar" required><br>
        Tinggi <br><input type="number" name="tinggi" required><br><br>
        
        Pilih Hitung:<br>
        <input type="radio" name="hitung" value="volume" checked> Volume<br>
        <input type="radio" name="hitung" value="luas"> Luas Permukaan<br>
        <input type="radio" name="hitung" value="keliling"> Keliling<br><br> 
        
        <input type="submit" value="Hitung">
        <input type="reset" value="Batal">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $panjang = $_POST['panjang'];
        $lebar = $_POST['lebar'];
        $tinggi = $_POST['tinggi'];
        $hitung = $_POST['hitung'];

        echo "<h3>HASIL PERHITUNGAN</h3>";
        echo "======================<br>";
        echo "Nilai Panjang: $panjang <br>";
        echo "Nilai Lebar: $lebar <br>";
        echo "Nilai Tinggi: $tinggi <br>";

        if ($hitung == "volume") {
            $hasil = $panjang * $lebar * $tinggi;
            echo "Maka Volume adalah: $hasil";
        } elseif ($hitung == "luas") {
            $hasil = 2 * (($panjang * $lebar) + ($panjang * $tinggi) + ($lebar * $tinggi));
            echo "Maka Luas Permukaan adalah: $hasil";
        } elseif ($hitung == "keliling") {
            $hasil = 4 * ($panjang + $lebar + $tinggi);
            echo "Maka Keliling adalah: $hasil";
        }
    }
    ?>
</body>
</html>
