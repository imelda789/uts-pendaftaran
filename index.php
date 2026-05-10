<?php
session_start();

// Inisialisasi session agar data tersimpan sementara tanpa database
if (!isset($_SESSION['data_pendaftaran'])) {
    $_SESSION['data_pendaftaran'] = [];
}

// Logika Proses Data
if (isset($_POST['simpan'])) {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $jk   = $_POST['jk'];
    $tmp  = $_POST['tmp'];
    $tgl  = $_POST['tgl'];
    $asal = $_POST['asal'];
    $ortu = $_POST['ortu'];
    $bin  = (int)$_POST['bin'];
    $mat  = (int)$_POST['mat'];
    $ingg = (int)$_POST['ingg'];
    $umum = (int)$_POST['umum'];

    // 1. Logika Tempat Tes Berdasarkan karakter awal Kode
    $dua_awal = strtoupper(substr($kode, 0, 1));
    if ($dua_awal == 'A') $tempat = "Gedung A";
    elseif ($dua_awal == 'B') $tempat = "Gedung B";
    elseif ($dua_awal == 'V') $tempat = "Viktor";
    else $tempat = "-";

    // 2. Logika Rata-rata & Keterangan
    $rata = ($bin + $mat + $ingg + $umum) / 4;
    if ($rata >= 70) $ket = "Lulus";
    elseif ($rata >= 60) $ket = "Cadangan";
    else $ket = "Tidak Lulus";

    // Simpan data ke array session
    $_SESSION['data_pendaftaran'][] = [
        'kode' => $kode, 'nama' => $nama, 'jk' => $jk, 'tmp' => $tmp, 'tgl' => $tgl,
        'ortu' => $ortu, 'tempat' => $tempat, 'bin' => $bin, 'mat' => $mat,
        'ingg' => $ingg, 'umum' => $umum, 'rata' => $rata, 'ket' => $ket
    ];

    // SOLUSI AGAR TIDAK DOUBLE: Redirect ke halaman sendiri setelah simpan
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Logika Reset Tabel
if (isset($_POST['reset'])) {
    $_SESSION['data_pendaftaran'] = [];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Perhitungan Statistik untuk bagian bawah tabel
$total_pendaftar = count($_SESSION['data_pendaftaran']);
$lulus = 0; 
$tidak_lulus = 0;
foreach ($_SESSION['data_pendaftaran'] as $row) {
    if ($row['ket'] == "Lulus") $lulus++;
    if ($row['ket'] == "Tidak Lulus") $tidak_lulus++;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Pendaftaran Mahasiswa</title>
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg: #f8fafc;
        }

        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background-color: var(--bg);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        h2 { border-bottom: 2px solid var(--primary); padding-bottom: 10px; color: var(--primary); font-size: 18px; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group { margin-bottom: 12px; }
        label { display: block; font-weight: 600; font-size: 12px; margin-bottom: 5px; color: var(--secondary); }
        
        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn-save { background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-reset { background: #334155; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        
        /* Table Styling */
        .table-wrapper { overflow-x: auto; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #f1f5f9; padding: 10px; border: 1px solid #e2e8f0; text-transform: uppercase; }
        td { padding: 10px; border: 1px solid #e2e8f0; text-align: center; }

        .badge { padding: 3px 8px; border-radius: 4px; color: white; font-weight: bold; font-size: 10px; }
        .bg-lulus { background: var(--success); }
        .bg-cadangan { background: var(--warning); }
        .bg-gagal { background: var(--danger); }

        .summary { display: flex; justify-content: flex-end; gap: 20px; margin-top: 20px; }
        .card { background: #f1f5f9; padding: 10px 20px; border-radius: 6px; text-align: center; }
        .card span { display: block; font-size: 18px; font-weight: bold; color: var(--primary); }
    </style>
</head>
<body>

<div class="container">
    <h2>INPUT PENDAFTARAN</h2>
    <form method="POST">
        <div class="form-grid">
            <div>
                <div class="form-group">
                    <label>JUMLAH INPUT DATA</label>
                    <input type="number" name="jumlah_input">
                </div>
                <div class="form-group">
                    <label>KODE PENDAFTARAN</label>
                    <input type="text" name="kode" required>
                </div>
                <div class="form-group">
                    <label>NAMA PENDAFTAR</label>
                    <input type="text" name="nama" required>
                </div>
                <div class="form-group">
                    <label>JENIS KELAMIN</label>
                    <input type="radio" name="jk" value="Laki-Laki" checked> Laki-Laki
                    <input type="radio" name="jk" value="Perempuan"> Perempuan
                </div>
                <div class="form-group">
                    <label>TTL (TEMPAT / TANGGAL LAHIR)</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="text" name="tmp" placeholder="Kota">
                        <input type="date" name="tgl">
                    </div>
                </div>
                <div class="form-group">
                    <label>ASAL SEKOLAH & PEKERJAAN ORTU</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="text" name="asal" placeholder="Sekolah">
                        <input type="text" name="ortu" placeholder="Pekerjaan">
                    </div>
                </div>
            </div>

            <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <label style="color: var(--primary); font-size: 14px; margin-bottom: 10px;">NILAI TES</label>
                <div class="form-group">
                    <label>BHS. INDONESIA</label>
                    <input type="number" name="bin">
                </div>
                <div class="form-group">
                    <label>MATEMATIKA</label>
                    <input type="number" name="mat">
                </div>
                <div class="form-group">
                    <label>BHS. INGGRIS</label>
                    <input type="number" name="ingg">
                </div>
                <div class="form-group">
                    <label>P. UMUM</label>
                    <input type="number" name="umum">
                </div>
            </div>
        </div>
        
        <button type="submit" name="simpan" class="btn-save">SIMPAN DATA</button>
        <button type="submit" name="reset" class="btn-reset">RESET</button>
    </form>

    <h2>TABEL DATA</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Pendaftar</th>
                    <th>JK</th>
                    <th>Tgl Lahir</th>
                    <th>Pekr. Ortu</th>
                    <th>Tempat Tes</th>
                    <th>Bin</th>
                    <th>Mat</th>
                    <th>Ingg</th>
                    <th>Umum</th>
                    <th>Rerata</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['data_pendaftaran'] as $d): 
                    $badge = ($d['ket'] == "Lulus") ? "bg-lulus" : (($d['ket'] == "Cadangan") ? "bg-cadangan" : "bg-gagal");
                ?>
                <tr>
                    <td><strong><?= $d['kode'] ?></strong></td>
                    <td><?= $d['nama'] ?></td>
                    <td><?= $d['jk'] ?></td>
                    <td><?= $d['tmp'] . ", " . $d['tgl'] ?></td>
                    <td><?= $d['ortu'] ?></td>
                    <td><?= $d['tempat'] ?></td>
                    <td><?= $d['bin'] ?></td>
                    <td><?= $d['mat'] ?></td>
                    <td><?= $d['ingg'] ?></td>
                    <td><?= $d['umum'] ?></td>
                    <td><strong><?= number_format($d['rata'], 2) ?></strong></td>
                    <td><span class="badge <?= $badge ?>"><?= strtoupper($d['ket']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="summary">
        <div class="card"><label>TOTAL PENDAFTAR</label><span><?= $total_pendaftar ?></span></div>
        <div class="card"><label>LULUS</label><span style="color: var(--success);"><?= $lulus ?></span></div>
        <div class="card"><label>TIDAK LULUS</label><span style="color: var(--danger);"><?= $tidak_lulus ?></span></div>
    </div>
</div>

</body>
</html>