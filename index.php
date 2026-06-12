<?php
include "auth.php";
include "koneksi.php";

$queryTerbaru = mysqli_query($conn, "SELECT * FROM monitoring ORDER BY id DESC LIMIT 1");
$data = mysqli_fetch_assoc($queryTerbaru);

$queryRiwayat = mysqli_query($conn, "SELECT * FROM monitoring ORDER BY id DESC LIMIT 10");
$queryGrafik = mysqli_query($conn, "SELECT * FROM monitoring ORDER BY id DESC LIMIT 12");

$grafik = [];
while ($g = mysqli_fetch_assoc($queryGrafik)) {
    $grafik[] = $g;
}
$grafik = array_reverse($grafik);

$suhu = $data ? $data['suhu'] : 0;
$turbidity = $data ? $data['turbidity'] : 0;
$tds = $data ? $data['tds'] : 0;
$status_air = $data ? $data['status_air'] : "-";
$status_tds = $data ? $data['status_tds'] : "-";
$status_pompa = $data ? $data['status_pompa'] : "OFF";
$waktu = $data ? $data['waktu'] : "-";

function persen($nilai, $max) {
    if ($max <= 0) return 0;
    $p = ($nilai / $max) * 100;
    if ($p < 0) $p = 0;
    if ($p > 100) $p = 100;
    return round($p);
}

function warnaStatus($status) {
    if ($status == "JERNIH" || $status == "NORMAL" || $status == "ON") {
        return "#35e6a1";
    } elseif ($status == "SEDANG") {
        return "#ffc857";
    } elseif ($status == "KERUH" || $status == "TINGGI" || $status == "OFF") {
        return "#ff5f6d";
    } else {
        return "#9ca3af";
    }
}

$suhuPersen = persen($suhu, 50);
$turbidityPersen = persen($turbidity, 4095);
$tdsPersen = persen($tds, 1000);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kolam Ikan Koki</title>
    <meta http-equiv="refresh" content="8">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #1f1b5c, #4c5dff);
            color: white;
            min-height: 100vh;
        }

        .navbar {
            background: rgba(19, 18, 66, 0.95);
            padding: 18px 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
        }

        .logo span {
            color: #35e6a1;
        }

        .menu {
            display: flex;
            gap: 15px;
        }

        .menu button {
            background: transparent;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
            padding: 10px 14px;
            border-radius: 10px;
            transition: 0.2s;
        }

        .menu button:hover,
        .menu button.active {
            background: #35e6a1;
            color: #151547;
            font-weight: bold;
        }

        .online {
            background: rgba(53, 230, 161, 0.18);
            color: #35e6a1;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .container {
            padding: 28px;
        }

        .page {
            display: none;
        }

        .page.active-page {
            display: block;
        }

        .title-box {
            margin-bottom: 22px;
        }

        .title-box h1 {
            margin: 0;
            font-size: 32px;
        }

        .title-box p {
            margin-top: 8px;
            color: #cfd3ff;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        .card {
            background: rgba(29, 29, 99, 0.9);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.22);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            height: 4px;
            width: 100%;
            background: linear-gradient(90deg, #35e6a1, #4c7dff);
        }

        .card-title {
            color: #cfd3ff;
            font-size: 15px;
            margin-bottom: 14px;
        }

        .main-value {
            font-size: 38px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .unit {
            color: #aab0ff;
            font-size: 14px;
        }

        .wide {
            grid-column: span 2;
        }

        .full {
            grid-column: span 4;
        }

        .bar {
            height: 13px;
            background: rgba(255,255,255,0.13);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 14px;
        }

        .bar-fill {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, #35e6a1, #4c7dff);
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .status-item {
            background: rgba(255,255,255,0.08);
            padding: 20px;
            border-radius: 14px;
            text-align: center;
        }

        .status-label {
            color: #cfd3ff;
            margin-bottom: 10px;
        }

        .badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            color: #111;
            font-weight: bold;
        }

        .chart {
            height: 220px;
            display: flex;
            align-items: end;
            gap: 8px;
            padding-top: 15px;
        }

        .chart-bar {
            flex: 1;
            background: linear-gradient(180deg, #35e6a1, #4c7dff);
            border-radius: 7px 7px 0 0;
            min-height: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 14px;
        }

        th {
            background: rgba(255,255,255,0.12);
            color: #cfd3ff;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        tr:hover {
            background: rgba(255,255,255,0.06);
        }

        .sensor-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .sensor-card h3 {
            margin-top: 0;
            color: #35e6a1;
        }

        .sensor-card p {
            color: #d7dbff;
            line-height: 1.6;
        }

        .footer {
            margin-top: 22px;
            text-align: center;
            color: #cfd3ff;
            font-size: 13px;
        }

        @media (max-width: 900px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .full,
            .wide {
                grid-column: span 2;
            }

            .sensor-list {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .navbar {
                flex-direction: column;
                gap: 12px;
            }

            .menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .full,
            .wide {
                grid-column: span 1;
            }

            .status-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="navbar">
    <div class="logo">KolamIkan<span>.IoT</span></div>

   <div class="menu">
    <button class="nav-btn active" data-page="dashboard" onclick="showPage('dashboard', this)">Dashboard</button>
    <button class="nav-btn" data-page="monitoring" onclick="showPage('monitoring', this)">Monitoring</button>
    <button class="nav-btn" data-page="sensor" onclick="showPage('sensor', this)">Sensor</button>
    <button class="nav-btn" data-page="database" onclick="showPage('database', this)">Database</button>
</div>

    <div style="display:flex; gap:12px; align-items:center;">
    <div class="online">● ONLINE</div>
    <a href="logout.php" style="
        color:white;
        background:rgba(255,95,109,0.25);
        padding:8px 14px;
        border-radius:18px;
        text-decoration:none;
        font-size:13px;
        font-weight:bold;
    ">Logout</a>
</div>
</div>

<div class="container">

    <!-- ================= DASHBOARD ================= -->
    <div id="dashboard" class="page active-page">
        <div class="title-box">
            <h1>Dashboard Kolam Ikan Koki</h1>
            <p>Monitoring suhu, kekeruhan air, TDS, dan status pompa secara real-time.</p>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-title">Suhu Air</div>
                <div class="main-value"><?php echo round($suhu, 1); ?></div>
                <div class="unit">°C</div>
                <div class="bar">
                    <div class="bar-fill" style="width: <?php echo $suhuPersen; ?>%;"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">Kekeruhan Air</div>
                <div class="main-value"><?php echo $turbidity; ?></div>
                <div class="unit">ADC / 4095</div>
                <div class="bar">
                    <div class="bar-fill" style="width: <?php echo $turbidityPersen; ?>%;"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">TDS</div>
                <div class="main-value"><?php echo round($tds, 1); ?></div>
                <div class="unit">ppm</div>
                <div class="bar">
                    <div class="bar-fill" style="width: <?php echo $tdsPersen; ?>%;"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">Update Terakhir</div>
                <div style="font-size: 20px; line-height: 1.5;"><?php echo $waktu; ?></div>
                <div class="unit">Data dari ESP32</div>
            </div>

            <div class="card full">
                <div class="card-title">Status Sistem</div>
                <div class="status-grid">
                    <div class="status-item">
                        <div class="status-label">Status Air</div>
                        <span class="badge" style="background: <?php echo warnaStatus($status_air); ?>;">
                            <?php echo $status_air; ?>
                        </span>
                    </div>

                    <div class="status-item">
                        <div class="status-label">Status TDS</div>
                        <span class="badge" style="background: <?php echo warnaStatus($status_tds); ?>;">
                            <?php echo $status_tds; ?>
                        </span>
                    </div>

                    <div class="status-item">
                        <div class="status-label">Status Pompa</div>
                        <span class="badge" style="background: <?php echo warnaStatus($status_pompa); ?>;">
                            <?php echo $status_pompa; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MONITORING ================= -->
    <div id="monitoring" class="page">
        <div class="title-box">
            <h1>Monitoring Riwayat Data</h1>
            <p>Menampilkan grafik sederhana berdasarkan data terakhir yang tersimpan di database.</p>
        </div>

        <div class="grid">
            <div class="card wide">
                <div class="card-title">Grafik Turbidity</div>
                <div class="chart">
                    <?php foreach ($grafik as $row): ?>
                        <?php 
                            $h = persen($row['turbidity'], 4095);
                            if ($h < 8) $h = 8;
                        ?>
                        <div class="chart-bar" style="height: <?php echo $h; ?>%;"></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card wide">
                <div class="card-title">Grafik TDS</div>
                <div class="chart">
                    <?php foreach ($grafik as $row): ?>
                        <?php 
                            $h = persen($row['tds'], 1000);
                            if ($h < 8) $h = 8;
                        ?>
                        <div class="chart-bar" style="height: <?php echo $h; ?>%;"></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card full">
                <div class="card-title">Keterangan Monitoring</div>
                <p style="line-height: 1.7; color: #d7dbff;">
                    Sistem membaca suhu air, kekeruhan air, dan nilai TDS dari sensor yang terhubung ke ESP32.
                    Data dikirim melalui WiFi ke server lokal XAMPP, kemudian disimpan pada database MySQL.
                    Nilai kekeruhan digunakan untuk menentukan kondisi air, sedangkan status pompa menunjukkan
                    apakah pompa filtrasi sedang aktif atau tidak.
                </p>
            </div>
        </div>
    </div>

    <!-- ================= SENSOR ================= -->
    <div id="sensor" class="page">
        <div class="title-box">
            <h1>Informasi Sensor dan Komponen</h1>
            <p>Komponen utama yang digunakan pada sistem monitoring dan filtrasi air.</p>
        </div>

        <div class="sensor-list">
            <div class="card sensor-card">
                <h3>ESP32 DevKit V1</h3>
                <p>Berfungsi sebagai pusat kendali sistem, membaca data sensor, mengontrol relay, dan mengirim data ke database melalui WiFi.</p>
            </div>

            <div class="card sensor-card">
                <h3>Sensor Suhu DS18B20</h3>
                <p>Digunakan untuk membaca suhu air kolam ikan koki. Sensor ini terhubung ke GPIO 4 pada ESP32.</p>
            </div>

            <div class="card sensor-card">
                <h3>Sensor Turbidity</h3>
                <p>Digunakan untuk membaca tingkat kekeruhan air. Nilai tinggi menunjukkan air lebih jernih, sedangkan nilai rendah menunjukkan air lebih keruh.</p>
            </div>

            <div class="card sensor-card">
                <h3>Sensor TDS</h3>
                <p>Digunakan untuk membaca zat terlarut dalam air. Pada sistem ini TDS menjadi parameter tambahan untuk memantau kualitas air.</p>
            </div>

            <div class="card sensor-card">
                <h3>Relay 1 Channel</h3>
                <p>Berfungsi sebagai saklar otomatis untuk mengaktifkan dan mematikan pompa air berdasarkan kondisi sensor.</p>
            </div>

            <div class="card sensor-card">
                <h3>Pompa Air Mini</h3>
                <p>Digunakan sebagai aktuator untuk membantu sirkulasi dan filtrasi air saat kondisi air terdeteksi keruh.</p>
            </div>
        </div>
    </div>

    <!-- ================= DATABASE ================= -->
    <div id="database" class="page">
        <div class="title-box">
            <h1>Database Monitoring</h1>
            <p>Data terakhir yang tersimpan pada database MySQL melalui phpMyAdmin.</p>
        </div>

        <div class="card full">
            <div class="card-title">Riwayat Data Terakhir</div>

            <table>
                <tr>
                    <th>No</th>
                    <th>Suhu</th>
                    <th>Turbidity</th>
                    <th>TDS</th>
                    <th>Status Air</th>
                    <th>Status TDS</th>
                    <th>Pompa</th>
                    <th>Waktu</th>
                </tr>

                <?php
                $no = 1;
                while ($r = mysqli_fetch_assoc($queryRiwayat)) {
                    echo "<tr>";
                    echo "<td>".$no++."</td>";
                    echo "<td>".$r['suhu']." °C</td>";
                    echo "<td>".$r['turbidity']."</td>";
                    echo "<td>".$r['tds']." ppm</td>";
                    echo "<td>".$r['status_air']."</td>";
                    echo "<td>".$r['status_tds']."</td>";
                    echo "<td>".$r['status_pompa']."</td>";
                    echo "<td>".$r['waktu']."</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <div class="footer">
        Sistem Monitoring dan Filtrasi Air Kolam Ikan Koki Otomatis Berbasis Internet of Things
    </div>

</div>

<script>
function showPage(pageId, button) {
    const pages = document.querySelectorAll('.page');
    const buttons = document.querySelectorAll('.nav-btn');

    pages.forEach(page => {
        page.classList.remove('active-page');
    });

    buttons.forEach(btn => {
        btn.classList.remove('active');
    });

    document.getElementById(pageId).classList.add('active-page');
    button.classList.add('active');

    // Simpan menu terakhir yang dibuka
    localStorage.setItem('menuAktif', pageId);
}

document.addEventListener('DOMContentLoaded', function () {
    const menuAktif = localStorage.getItem('menuAktif') || 'dashboard';

    const targetPage = document.getElementById(menuAktif);
    const targetButton = document.querySelector('.nav-btn[data-page="' + menuAktif + '"]');

    if (targetPage && targetButton) {
        document.querySelectorAll('.page').forEach(page => {
            page.classList.remove('active-page');
        });

        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        targetPage.classList.add('active-page');
        targetButton.classList.add('active');
    }
});
</script>

</body>
</html>